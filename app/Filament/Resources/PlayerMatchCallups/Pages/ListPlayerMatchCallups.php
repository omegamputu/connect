<?php

namespace App\Filament\Resources\PlayerMatchCallups\Pages;

use App\Filament\Resources\PlayerMatchCallups\PlayerMatchCallupResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPlayerMatchCallups extends ListRecords
{
    protected static string $resource = PlayerMatchCallupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
