<?php

namespace App\Filament\Resources\FootballMatches\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FootballMatchesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with([
                'homeTeam',
                'awayTeam',
                'competition',
                'category',
            ]))
            ->columns([
                TextColumn::make('competition.name')
                    ->label('Competition')
                    ->badge()
                    ->searchable(),
                TextColumn::make('Teams')
                    ->label('Teams')
                    ->getStateUsing(fn ($record) => "{$record->homeTeam->name} vs {$record->awayTeam->name}")
                    ->searchable(fn (Builder $query, string $search) => $query->whereHas('homeTeam', fn (Builder $q) => $q->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('awayTeam', fn (Builder $q) => $q->where('name', 'like', "%{$search}%"))),
                TextColumn::make('match_date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->badge()
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (string $state) => ucfirst($state))
                    ->badge()
                    ->sortable(),
                TextColumn::make('score')
                    ->label('Score')
                    ->getStateUsing(fn ($record) => is_null($record->home_score) || is_null($record->away_score) ? 'N/A' : "{$record->home_score} - {$record->away_score}"),
            ])
            ->filters([
                SelectFilter::make('competition_id')
                    ->relationship('competition', 'name')
                    ->label('Competition'),
                SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Category'),
                SelectFilter::make('season_id')
                    ->relationship('season', 'name')
                    ->label('Season'),
                SelectFilter::make('status')
                    ->options([
                        'scheduled' => 'Scheduled',
                        'played' => 'Played',
                        'cancelled' => 'Cancelled',
                        'postponed' => 'Postponed',
                        'abandoned' => 'Abandoned',
                    ])
                    ->label('Status'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->mutateDataUsing(function (array $data): array {
                        $data['updated_by'] = auth()->id();

                        return $data;
                    }),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
