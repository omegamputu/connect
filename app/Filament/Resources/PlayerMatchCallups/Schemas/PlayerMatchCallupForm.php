<?php

namespace App\Filament\Resources\PlayerMatchCallups\Schemas;

use App\Models\Player;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
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
                            ->required(),
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
                            ->relationship('team', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
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
