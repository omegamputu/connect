<?php

namespace App\Filament\Resources\FootballMatches\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FootballMatchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Match Details')
                    ->schema([
                        Select::make('competition_id')
                            ->relationship('competition', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('season_id')
                            ->relationship('season', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])->columns(3),
                Section::make('Teams')
                ->schema([
                    Select::make('home_team_id')
                        ->relationship('homeTeam', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Select::make('away_team_id')
                        ->relationship('awayTeam', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                ])->columns(2),
                Section::make('Match Information')
                    ->schema([
                        TextInput::make('venue')
                            ->maxLength(150),
                        TextInput::make('city')
                            ->maxLength(100),
                        TextInput::make('country')
                            ->maxLength(100),
                        TextInput::make('match_number')
                            ->numeric()
                            ->minValue(50),
                        // TextInput::make('external_reference'),
                        DatePicker::make('match_date')
                            ->native(false)
                            ->required(),
                        TimePicker::make('kickoff_time'),
                        Select::make('status')
                            ->options([
                                'scheduled' => 'Scheduled',
                                'played' => 'Played',
                                'cancelled' => 'Cancelled',
                                'postponed' => 'Postponed',
                                'abandoned' => 'Abandoned',
                            ])
                            ->default('scheduled')
                            ->required(),
                        TextInput::make('stage')
                            ->maxLength(100)
                            ->placeholder('Group Stage, Quarterfinals, etc.'),
                        TextInput::make('leg')
                            ->numeric()
                            ->minValue(1)
                            ->label('Leg (for knockout rounds)')
                            ->placeholder('1 for first leg, 2 for second leg, etc.'),
                    ])->columns(3),
                Section::make('Scores')
                    ->schema([
                        TextInput::make('home_score')
                            ->numeric()
                            ->minValue(0),
                        TextInput::make('away_score')
                            ->numeric()
                            ->minValue(0),
                        TextInput::make('home_score_ht')
                            ->numeric()
                            ->minValue(0)
                            ->label('Home Score (HT)'),
                        TextInput::make('away_score_ht')
                            ->numeric()
                            ->minValue(0)
                            ->label('Away Score (HT)'),
                    ])
                    ->columns(2),
                
                Section::make('Additional Information')
                    ->schema([
                        Textarea::make('notes')
                            ->rows(4)
                            ->columnSpanFull(),
                        Toggle::make('is_neutral_venue')
                            ->label('Neutral Venue')
                            ->default(false),
                        Toggle::make('is_international')
                            ->label('International Match')
                            ->default(true),
                    ]),
                Hidden::make('created_by')
                    ->default(fn () => auth()->id())
                    ->dehydrated(fn ($record) => $record === null),
                Hidden::make('updated_by')
                    ->default(fn () => auth()->id()),
            ])->columns(1);
    }
}
