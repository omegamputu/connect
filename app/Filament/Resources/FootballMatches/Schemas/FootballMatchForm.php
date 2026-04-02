<?php

namespace App\Filament\Resources\FootballMatches\Schemas;

use App\Models\Category;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
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
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('home_team_id', null);
                                $set('away_team_id', null);
                            })
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Category Name')
                                    ->required(),
                                TextInput::make('code')
                                    ->label('Code')
                                    ->required()
                                    ->unique(table: 'categories', column: 'code'),
                                TextInput::make('age_group')
                                    ->label('Age Group')
                                    ->required(),
                                Toggle::make('is_active')
                                    ->default(true)
                                    ->required(),
                            ])->createOptionUsing(function (array $data) {
                                $category = Category::firstOrCreate([
                                    'name' => $data['name'],
                                    'code' => $data['code'],
                                    'age_group' => $data['age_group'],
                                    'is_active' => $data['is_active'] ?? true,
                                    'created_by' => auth()->id(),
                                    'updated_by' => auth()->id(),
                                ]);

                                return $category->id;
                            }),
                        Select::make('season_id')
                            ->relationship('season', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])->columns(2)->contained(false),
                Section::make('Teams')
                ->schema([
                    Select::make('home_team_id')
                        ->label('Home Team')
                        ->options(function (Get $get) {
                            $categoryId = $get('category_id');

                            if (!$categoryId) {
                                return [];
                            }
                            return \App\Models\Team::query()
                                ->where('category_id', $categoryId)
                                ->where('is_active', true)
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->toArray();
                        })
                        ->searchable()
                        ->preload()
                        ->required()
                        ->live()
                        ->rules('different:away_team_id')
                        ->disableOptionWhen(fn ($value, Get $get): bool => (string) $value === (string) $get('away_team_id'))
                        ->createOptionForm([
                            TextInput::make('name')
                                ->label('Team Name')
                                ->required(),
                            TextInput::make('code')
                                ->label('Code')
                                ->required()
                                ->unique(table: 'teams', column: 'code'),
                            Select::make('category_id')
                                ->label('Category')
                                ->relationship('category', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            Toggle::make('is_active')
                                ->label('Active')
                                ->default(true)
                                ->onColor('success')
                                ->offColor('danger'),
                        ])->createOptionUsing(function (array $data) {
                            $team = \App\Models\Team::firstOrCreate([
                                'name' => $data['name'],
                                'code' => $data['code'],
                                'category_id' => $data['category_id'],
                                'is_active' => $data['is_active'] ?? true,
                                'created_by' => auth()->id(),
                                'updated_by' => auth()->id(),
                            ]);

                            return $team->id;
                        }),
                    Select::make('away_team_id')
                        ->label('Away Team')
                        ->options(function (Get $get) {
                            $categoryId = $get('category_id');

                            if (!$categoryId) {
                                return [];
                            }
                            return \App\Models\Team::query()
                                ->where('category_id', $categoryId)
                                ->where('is_active', true)
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->toArray();
                        })
                        ->searchable()
                        ->preload()
                        ->required()
                        ->live()
                        ->rules('different:home_team_id')
                        ->validationMessages([
                            'different' => 'The away team must be different from the home team.',
                        ])
                        ->disableOptionWhen(fn ($value, Get $get): bool => (string) $value === (string) $get('home_team_id'))
                        ->createOptionForm([
                            TextInput::make('name')
                                ->label('Team Name')
                                ->required(),
                            TextInput::make('code')
                                ->label('Code')
                                ->required()
                                ->unique(table: 'teams', column: 'code'),
                            Select::make('category_id')
                                ->label('Category')
                                ->relationship('category', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            Toggle::make('is_active')
                                ->label('Active')
                                ->default(true)
                                ->onColor('success')
                                ->offColor('danger'),
                        ])->createOptionUsing(function (array $data) {
                            $team = \App\Models\Team::firstOrCreate([
                                'name' => $data['name'],
                                'code' => $data['code'],
                                'category_id' => $data['category_id'],
                                'is_active' => $data['is_active'] ?? true,
                                'created_by' => auth()->id(),
                                'updated_by' => auth()->id(),
                            ]);

                            return $team->id;
                        }),
                ])->columns(3)->contained(false),
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
                            ->required()
                            ->live(),
                        TextInput::make('stage')
                            ->maxLength(100)
                            ->placeholder('Group Stage, Quarterfinals, etc.'),
                        TextInput::make('leg')
                            ->numeric()
                            ->minValue(1)
                            ->label('Leg')
                            ->placeholder('1 for first leg, 2 for second leg, etc.'),
                    ])->columns(3)->contained(false),
                Section::make('Scores')
                    ->schema([
                        TextInput::make('home_score')
                            ->numeric()
                            ->minValue(0)
                            ->required(fn (Get $get): bool => $get('status') === 'played'),
                        TextInput::make('away_score')
                            ->numeric()
                            ->minValue(0)
                            ->required(fn (Get $get): bool => $get('status') === 'played'),
                        TextInput::make('home_score_ht')
                            ->numeric()
                            ->minValue(0)
                            ->label('Home Score (HT)'),
                        TextInput::make('away_score_ht')
                            ->numeric()
                            ->minValue(0)
                            ->label('Away Score (HT)'),
                    ])
                    ->columns(4)
                    ->contained(false)
                    ->visible(fn (Get $get) => $get('status') === 'played'),
                
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
                    ])->contained(false),
                Hidden::make('created_by')
                    ->default(fn () => auth()->id())
                    ->dehydrated(fn ($record) => $record === null),
                Hidden::make('updated_by')
                    ->default(fn () => auth()->id()),
            ])->columns(1);
    }
}
