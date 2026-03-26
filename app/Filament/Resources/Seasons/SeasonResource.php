<?php

namespace App\Filament\Resources\Seasons;

use App\Filament\Resources\Seasons\Pages\ManageSeasons;
use App\Models\Season;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
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
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class SeasonResource extends Resource
{
    protected static ?string $model = Season::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Seasons';

    protected static ?string $pluralModelLabel = 'Seasons';

    protected static ?string $modelLabel = 'Season';

    protected static string | UnitEnum | null $navigationGroup = 'National Teams Management';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->maxLength(100)
                    ->placeholder('Example: 2025-2026')
                    ->required(),
                TextInput::make('code')
                    ->unique(ignoreRecord: true)
                    ->maxLength(30)
                    ->nullable()
                    ->placeholder('Example: 2025-26'),
                DatePicker::make('start_date')
                    ->native(false)
                    ->date('d/m/Y')
                    ->required(),
                DatePicker::make('end_date')
                    ->native(false)
                    ->after('start_date')
                    ->date('d/m/Y')
                    ->required(),
                Textarea::make('description')
                    ->rows(4)
                    ->columnSpanFull(),
                Toggle::make('is_current')
                    ->label('Current Season')
                    ->onColor('success')
                    ->offColor('danger')
                    ->default(false)
                    ->required(),
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->required(),
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
                TextEntry::make('code')
                    ->placeholder('-'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('start_date')
                    ->date('d/m/Y'),
                TextEntry::make('end_date')
                    ->date('d/m/Y'),
                IconEntry::make('is_current')
                    ->boolean(),
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
                    ->visible(fn (Season $record): bool => $record->trashed()),
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
                TextColumn::make('start_date')
                    ->label('Start Date')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('End Date')
                    ->date('d/m/Y')
                    ->sortable(),
                ToggleColumn::make('is_current')
                    ->label('Current')
                    ->afterStateUpdated(function ($record,$state) {
                        Notification::make()
                            ->title('Season Status Updated')
                            ->body($record->name . ' is now ' . ($state ? 'the current season' : 'not the current season'))
                            ->success()
                            ->send();
                    })
                    ->onColor('success')
                    ->offColor('gray'),
                ToggleColumn::make('is_active')
                    ->label('Active')
                    ->afterStateUpdated(function ($record,$state) {
                        Notification::make()
                            ->title('Status Updated')
                            ->body($record->name . ' is now ' . ($state ? 'active' : 'inactive'))
                            ->success()
                            ->send();
                    })
                    ->onColor('success')
                    ->offColor('danger'),
                TextColumn::make('createdBy.name')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updatedBy.name')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_current')
                    ->label('Current Season'),
                TernaryFilter::make('is_active')
                    ->label('Active'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                CreateAction::make()
                    ->mutateDataUsing(function (array $data): array{
                        $data['created_by'] = auth()->id();
                        $data['updated_by'] = auth()->id();

                        return $data;
                    }),
                EditAction::make()
                    ->mutateDataUsing(function (array $data): array{
                        $data['updated_by'] = auth()->id();
                        return $data;
                    }),
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
            'index' => ManageSeasons::route('/'),
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
