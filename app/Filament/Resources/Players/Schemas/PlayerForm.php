<?php

namespace App\Filament\Resources\Players\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Date;

class PlayerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Player Information')
                    ->icon(Heroicon::Identification)
                    ->components([
                        Grid::make()
                            ->schema([
                                TextInput::make('person.first_name')
                                    ->label('First Name')
                                    ->maxLength(100)
                                    ->required(),
                                TextInput::make('person.last_name')
                                    ->label('Last Name')
                                    ->maxLength(100)
                                    ->required(),
                                TextInput::make('person.usual_name')
                                    ->label('Usual Name')
                                    ->maxLength(100)
                                    ->nullable(),
                            ])
                            ->columns(2),
                        Grid::make()
                            ->schema([
                                DatePicker::make('person.birth_date')
                                    ->label('Birth Date')
                                    ->native(false),
                                Select::make('person.nationality_id')
                                    ->label('Nationality')
                                    ->relationship('person.nationality', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->default(47),
                                Select::make('person.gender')
                                    ->label('Gender')
                                    ->options([
                                        'male' => 'Male',
                                        'female' => 'Female'
                                    ])
                                    ->default('male')
                                    ->required(),
                            ])
                            ->columns(2),
                        
                    ])->columns(1)->contained(false),
                Section::make('Player Details')
                    ->icon(Heroicon::InformationCircle)
                    ->components([
                        Grid::make()
                            ->schema([
                                Select::make('sportInfo.position_id')
                                    ->label('Position')
                                    ->relationship('sportInfo.position', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->nullable(),
                                TextInput::make('sportInfo.jersey_name')
                                    ->label('Jersey Name')
                                    ->maxLength(100)
                                    ->nullable(),
                                TextInput::make('sportInfo.jersey_number')
                                    ->label('Jersey Number')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(99)
                                    ->nullable(),
                            ])
                            ->columns(3),
                        Grid::make()
                            ->schema([
                                TextInput::make('sportInfo.height_cm')
                                    ->label('Height (cm)')
                                    ->numeric()
                                    ->minValue(1)
                                    ->nullable(),
                                TextInput::make('sportInfo.weight_kg')
                                    ->label('Weight (kg)')
                                    ->numeric()
                                    ->minValue(1)
                                    ->nullable(),
                                Toggle::make('player.is_active')
                                    ->label('Is Active')
                                    ->default(true)
                                    ->inline(false)
                                    ->onColor('success')
                                    ->offColor('danger'),
                            ])
                            ->columns(2),
                    ])
                    ->columns(1)->contained(false),
            ]);
    }
}
