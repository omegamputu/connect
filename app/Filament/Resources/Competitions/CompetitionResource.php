<?php

namespace App\Filament\Resources\Competitions;

use App\Filament\Resources\Competitions\Pages\ManageCompetitions;
use App\Models\Competition;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class CompetitionResource extends Resource
{
    protected static ?string $model = Competition::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTrophy;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Competitions';

    protected static ?string $pluralModelLabel = 'Competitions';

    protected static ?string $modelLabel = 'Competition';

    protected static string | UnitEnum | null $navigationGroup = 'National Teams Management';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->maxLength(150)
                    ->placeholder('Qualification CAN U23')
                    ->required(),
                TextInput::make('code')
                    ->maxLength(50)
                    ->unique(ignoreRecord: true)
                    ->placeholder('QUALIF-CAN-U23')
                    ->required(),
                Select::make('competition_type')
                    ->options([
                        'tournament' => 'Tournament', 
                        'qualification' => 'Qualification', 
                        'friendly' => 'Friendly'
                        ])
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
                TextInput::make('organizer')
                    ->maxLength(100)
                    ->placeholder('Example: CAF'),
                Select::make('level')
                    ->options([
                        'international' => 'International', 
                        'continental' => 'Continental', 
                        'zonal' => 'Zonal'])
                    ->required(),
                Textarea::make('description')
                    ->rows(4)
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->required()
                    ->default(true),
                Hidden::make('created_by')
                    ->default(fn () => Auth::id())
                    ->dehydrated(fn ($record) => $record === null),
                Hidden::make('updated_by')
                    ->default(fn () => Auth::id()),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('code'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('competition_type')
                    ->badge(),
                TextEntry::make('category.name')
                    ->label('Category'),
                TextEntry::make('season.name')
                    ->label('Season'),
                TextEntry::make('organizer')
                    ->placeholder('-'),
                TextEntry::make('level')
                    ->badge(),
                IconEntry::make('is_active')
                    ->boolean(),
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
                    ->visible(fn (Competition $record): bool => $record->trashed()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('code')
                    ->searchable(),
                TextColumn::make('competition_type')
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->badge(),
                TextColumn::make('organizer')
                    ->searchable(),
                TextColumn::make('level')
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->badge(),
                ToggleColumn::make('is_active')
                    ->afterStateUpdated(function ($record,$state) {
                        Notification::make()
                            ->title('Status Updated')
                            ->body($record->name . ' is now ' . ($state ? 'active' : 'inactive'))
                            ->success()
                            ->send();
                    })
                    ->onColor('success')
                    ->offColor('danger'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Category')
                    ->placeholder('All Categories'),
                SelectFilter::make('season_id')
                    ->relationship('season', 'name')
                    ->label('Season')
                    ->placeholder('All Seasons'),
                SelectFilter::make('level')
                    ->options([
                        'international' => 'International', 
                        'continental' => 'Continental', 
                        'zonal' => 'Zonal'
                    ])
                    ->label('Level')
                    ->placeholder('All Levels'),
                SelectFilter::make('competition_type')
                    ->options([
                        'tournament' => 'Tournament', 
                        'qualification' => 'Qualification', 
                        'friendly' => 'Friendly'
                    ])
                    ->label('Type')
                    ->placeholder('All Types'),
                TernaryFilter::make('is_active')
                    ->label('Active')
                    ->placeholder('All Status'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCompetitions::route('/'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
