<?php

namespace App\Filament\Resources\PlayerMatchCallups\Schemas;

use App\Models\FootballMatch;
use App\Models\Player;
use App\Models\Team;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class PlayerMatchCallupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Callup information')
                    ->schema([
                        Select::make('match_id')
                            ->label('Match')
                            ->relationship('match', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => 
                                ($record->match_date?->format('d/m/Y') ?? '-') . ' - ' . 
                                ($record->homeTeam?->name ?? '-') . ' vs ' . 
                                ($record->awayTeam?->name ?? '-'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('team_id', null);
                            }),
                        Select::make('player_id')
                            ->label('Player')
                            ->options(function () {
                                return Player::query()->with('person')
                                    ->get()
                                    ->mapWithKeys(fn ($player) => [
                                        $player->id => $player->person?->usual_name ?? 'Unnamed Player',
                                    ]);
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Select an existing player. Use the Players section to create a new player.'),
                        Select::make('team_id')
                            ->label('Team')
                            ->options(function (Get $get) {
                                $matchId = $get('match_id');

                                if (!$matchId) {
                                    return [];
                                }

                                $match = FootballMatch::query()->find($matchId);

                                if (!$match) {
                                    return [];
                                }

                                return Team::query()
                                    ->whereIn('id', [$match->home_team_id, $match->away_team_id])
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->disabled(fn (Get $get): bool => blank($get('match_id')))
                            ->placeholder(fn (Get $get) => blank($get('match_id')) ? 'Select a match first' : 'Select a team'),
                        Select::make('position_id')
                            ->label('Position')
                            ->relationship('position', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('jersey_number')
                            ->label('Jersey Number')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(99),
                        Hidden::make('created_by')
                            ->default(fn () => auth()->id())
                            ->dehydrated(fn ($record) => $record === null),
                        Hidden::make('updated_by')
                            ->default(fn () => auth()->id()),
                    ])->columns(2)->contained(false),
            ]);
    }
}
