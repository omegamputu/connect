<?php

namespace App\Filament\Resources\Players\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClubRegistrationsRelationManager extends RelationManager
{
    protected static string $relationship = 'clubRegistrations';

     protected static ?string $title = 'Club Registrations';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->components([
                        Select::make('club_id')
                            ->label('Club')
                            ->relationship('club', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Club Name')
                                    ->required(),
                                Select::make('country_id')
                                    ->label('Country')
                                    ->relationship('country', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                            ])
                            ->createOptionUsing(function (array $data) {
                                $club = \App\Models\Club::firstOrCreate([
                                    'name' => $data['name'],
                                    'country_id' => $data['country_id'],
                                    'created_by' => auth()->id(),
                                    'updated_by' => auth()->id(),
                                ]);

                                return $club->id;
                            }),
                        DatePicker::make('registration_date')
                            ->label('Registration Date')
                            ->native(false)
                            ->required(),
                        Select::make('level')
                            ->label('Player Level')
                            ->options([
                                'professional' => 'Professional',
                                'no-professional' => 'No Professional',
                            ])
                            ->nullable(),
                        Select::make('is_current')
                            ->label('Is Current')
                            ->options([
                                1 => 'Yes',
                                0 => 'No',
                            ])
                            ->default(1)
                            ->required(),
                    ])->columns(1)->contained(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with([
                'club',
                'createdBy',
                'updatedBy',
            ]))
            ->columns([
                TextColumn::make('club.name')
                    ->label('Club')
                    ->searchable(),
                TextColumn::make('registration_date')
                    ->label('Registration Date')
                    ->date('d-m-Y'),
                TextColumn::make('level')
                    ->label('Player Level')
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->badge()
                    ->sortable(),
                ToggleColumn::make('is_current')
                    ->label('Current')
                    ->onColor('success')
                    ->offColor('gray')
                    ->afterStateUpdated(function ($state, $record) {
                        if ($state) {
                            $record->player->clubRegistrations()
                                ->where('id', '!=', $record->id)
                                ->update(['is_current' => false]);
                        }
                    }),
                TextColumn::make('createdBy.name')
                    ->label('Created By')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updatedBy.name')
                    ->label('Updated By')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_current')
                    ->label('Current'),
                TrashedFilter::make(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateDataUsing(function (array $data): array {
                        $data['created_by'] = auth()->id();
                        $data['updated_by'] = auth()->id();

                        return $data;
                    })
                    ->after(function ($record) {
                        if ($record->is_current) {
                            $record->player->clubRegistrations()
                                ->where('id', '!=', $record->id)
                                ->update(['is_current' => false]);
                        }
                    }),
                AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->mutateDataUsing(function (array $data) {
                        $data['updated_by'] = auth()->id();

                        return $data;
                    })
                    ->after(function ($record) {
                        if ($record->is_current) {
                            $record->player->clubRegistrations()
                                ->where('id', '!=', $record->id)
                                ->update(['is_current' => false]);
                        }
                    }),
                DissociateAction::make(),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->withoutGlobalScopes([
                    SoftDeletingScope::class,
                ]));
    }
}
