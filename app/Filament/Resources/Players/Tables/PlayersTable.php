<?php

namespace App\Filament\Resources\Players\Tables;

use Dom\Text;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PlayersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with([
                'person',
                'sportInfo',
                'currentClubRegistration',
            ]))
            ->columns([
                TextColumn::make('sportInfo.jersey_number')
                    ->label('No.')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('sportInfo.position.code')
                    ->label('Pos')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('person.usual_name')
                    ->label('Popular name')
                    ->searchable(),
                TextColumn::make('person.last_name')
                    ->label('Last name')
                    ->searchable(),
                TextColumn::make('person.first_name')
                    ->label('First name')
                    ->searchable(),
                TextColumn::make('sportInfo.jersey_name')
                    ->label('Shirt name')
                    ->searchable(),
                TextColumn::make('person.birth_date')
                    ->label('Birth date')
                    ->date('d-m-Y')
                    ->sortable(),
                TextColumn::make('currentClubRegistration.club.country.code')
                    ->label('Club country')
                    ->searchable(),
                TextColumn::make('currentClubRegistration.club.name')
                    ->label('Club name')
                    ->searchable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
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
