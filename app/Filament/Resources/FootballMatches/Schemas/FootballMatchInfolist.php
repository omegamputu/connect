<?php

namespace App\Filament\Resources\FootballMatches\Schemas;

use App\Models\FootballMatch;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FootballMatchInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Match Details')
                    ->schema([
                        TextEntry::make('competition.name')
                            ->label('Competition'),
                        TextEntry::make('category.name')
                            ->label('Category'),
                        TextEntry::make('season.name')
                            ->label('Season'),
                    ])->columns(3),

                Section::make('Teams & Venue')
                    ->schema([
                        TextEntry::make('homeTeam.name')
                            ->label('Home team'),
                        TextEntry::make('awayTeam.name')
                            ->label('Away team'),
                        TextEntry::make('venue')
                            ->placeholder('-'),
                        TextEntry::make('city')
                            ->placeholder('-'),
                        TextEntry::make('country')
                            ->placeholder('-'),
                    ])
                    ->columns(2),
                Section::make('Match Information')
                    ->schema([
                        TextEntry::make('match_number')
                            ->placeholder('-'),
                        TextEntry::make('external_reference')
                            ->placeholder('-'),
                        TextEntry::make('match_date')
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('kickoff_time')
                            ->time()
                            ->placeholder('-'),
                        TextEntry::make('status')
                            ->badge(),
                        TextEntry::make('stage')
                            ->placeholder('-'),
                        TextEntry::make('leg')
                            ->numeric()
                            ->placeholder('-'),
                    ])
                    ->columns(3),
                Section::make('Scores')
                    ->schema([
                        TextEntry::make('home_score')
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('away_score')
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('home_score_ht')
                            ->numeric()
                            ->label('Home Score (HT)')
                            ->placeholder('-'),
                        TextEntry::make('away_score_ht')
                            ->numeric()
                            ->label('Away Score (HT)')
                            ->placeholder('-'),
                    ])
                    ->columns(2),
                Section::make('Additional Information')
                    ->schema([
                        TextEntry::make('notes')
                            ->placeholder('-')
                            ->columnSpanFull(),
                        IconEntry::make('is_neutral_venue')
                            ->boolean(),
                        IconEntry::make('is_international')
                            ->boolean(),
                    ])
                    ->columns(1),
                Section::make('Audit Information')
                    ->schema([
                        TextEntry::make('createdBy.name')
                            ->label('Created By')
                            ->placeholder('-'),
                        TextEntry::make('updatedBy.name')
                            ->label('Updated By')
                            ->placeholder('-'),
                        TextEntry::make('created_at')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('deleted_at')
                            ->dateTime()
                            ->visible(fn (FootballMatch $record): bool => $record->trashed()),
                    ])
                    ->columns(2),
            ])->columns(1);
    }
}
