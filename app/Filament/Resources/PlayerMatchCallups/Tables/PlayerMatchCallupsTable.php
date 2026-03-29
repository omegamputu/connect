<?php

namespace App\Filament\Resources\PlayerMatchCallups\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use GuzzleHttp\Promise\Create;
use Illuminate\Database\Eloquent\Builder;

class PlayerMatchCallupsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with([
                'match.homeTeam',
                'match.awayTeam',
                'player.person',
                'player.sportInfo',
                'player.currentClubRegistration',
                'team',
                'position',
            ]))
            ->columns([
                TextColumn::make('jersey_number')
                    ->label('Shirt No.')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('position.code')
                    ->label('Position')
                    ->searchable(),
                TextColumn::make('player.person.usual_name')
                    ->label('Popular name')
                    ->searchable(),
                TextColumn::make('player.person.first_name')
                    ->label('First name')
                    ->searchable(),
                TextColumn::make('player.person.last_name')
                    ->label('Last name')
                    ->searchable(),
                TextColumn::make('player.person.birth_date')
                    ->label('Date of birth')
                    ->date('d-m-Y')
                    ->sortable(),
                TextColumn::make('player.currentClubRegistration.club.country.code')
                    ->label('Club country')
                    ->searchable(),
                TextColumn::make('player.currentClubRegistration.club.name')
                    ->label('Club name')
                    ->searchable(),
                TextColumn::make('match.match_date')
                    ->label('Match Date')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('match_display')
                    ->label('Match')
                    ->getStateUsing(fn ($record) => 
                        ($record->match?->homeTeam?->name ?? '-') . ' vs ' . 
                        ($record->match?->awayTeam?->name ?? '-')
                    )
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('match.homeTeam', fn (Builder $q) => $q->where('name', 'like', "%{$search}%"))
                            ->orWhereHas('match.awayTeam', fn (Builder $q) => $q->where('name', 'like', "%{$search}%"));
                    }),
                TextColumn::make('created_by')
                    ->label('Created By')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_by')
                    ->label('Updated By')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('team_id')
                    ->label('Team')
                    ->relationship('team', 'name'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->mutateDataUsing(function (array $data): array {
                        $data['updated_by'] = auth()->id();
                        
                        return $data;
                    }),
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
