<?php

namespace App\Filament\Resources\PlayerMatchCallups\Pages;

use App\Filament\Resources\PlayerMatchCallups\PlayerMatchCallupResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditPlayerMatchCallup extends EditRecord
{
    protected static string $resource = PlayerMatchCallupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
