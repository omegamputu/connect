<?php

namespace App\Filament\Resources\Teams;

use App\Filament\Resources\Teams\Pages\ManageTeams;
use App\Models\Team;
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
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;
use Illuminate\Support\Facades\Auth;

class TeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFlag;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Teams';

    protected static ?string $pluralModelLabel = 'Teams';

    protected static ?string $modelLabel = 'Team';

    protected static string | UnitEnum | null $navigationGroup = 'National Teams Management';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(150)
                    ->placeholder('Example: Leopards U17 Garçons'),
                TextInput::make('code')
                    ->unique(ignoreRecord: true)
                    ->maxLength(50)
                    ->placeholder('Example: LEOPARDS_U17_GARCONS'),
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
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Team $record): bool => $record->trashed()),
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
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->placeholder('-'),
                ToggleColumn::make('is_active')
                    ->label('Active')
                    ->afterStateUpdated(function ($record, $state) {
                        // You can perform additional actions here if needed
                        Notification::make()
                            ->title('Status Updated')
                            ->body($record->name . ' is now ' . ($state ? 'active' : 'inactive'))
                            ->success()
                            ->send();
                    })
                    ->onColor('success')
                    ->offColor('danger')
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->placeholder('All Categories'),
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
            'index' => ManageTeams::route('/'),
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
