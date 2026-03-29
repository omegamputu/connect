<?php

namespace App\Filament\Resources\PlayerMatchCallups;

use App\Filament\Resources\PlayerMatchCallups\Pages\CreatePlayerMatchCallup;
use App\Filament\Resources\PlayerMatchCallups\Pages\EditPlayerMatchCallup;
use App\Filament\Resources\PlayerMatchCallups\Pages\ListPlayerMatchCallups;
use App\Filament\Resources\PlayerMatchCallups\Schemas\PlayerMatchCallupForm;
use App\Filament\Resources\PlayerMatchCallups\Tables\PlayerMatchCallupsTable;
use App\Models\PlayerMatchCallup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlayerMatchCallupResource extends Resource
{
    protected static ?string $model = PlayerMatchCallup::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $navigationLabel = 'Player Callups';

    protected static ?string $modelLabel = 'Player Callup';

    protected static ?string $pluralModelLabel = 'Player Callups';

    protected static string|\UnitEnum|null $navigationGroup = 'National Teams Management';

    protected static ?int $navigationSort = 8;

    public static function form(Schema $schema): Schema
    {
        return PlayerMatchCallupForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PlayerMatchCallupsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPlayerMatchCallups::route('/'),
            'create' => CreatePlayerMatchCallup::route('/create'),
            'edit' => EditPlayerMatchCallup::route('/{record}/edit'),
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
