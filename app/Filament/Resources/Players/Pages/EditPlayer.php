<?php

namespace App\Filament\Resources\Players\Pages;

use App\Filament\Resources\Players\PlayerResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditPlayer extends EditRecord
{
    protected static string $resource = PlayerResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['person'] = [
            'first_name' => $this->record->person->first_name,
            'last_name' => $this->record->person->last_name,
            'usual_name' => $this->record->person->usual_name,
            'birth_date' => $this->record->person->birth_date,
            'gender' => $this->record->person->gender,
            'nationality_id' => $this->record->person->nationality_id,
        ];

        $data['sportInfo'] = [
            'position_id' => $this->record->sportInfo->position_id ?? null,
            'jersey_name' => $this->record->sportInfo->jersey_name ?? null,
            'jersey_number' => $this->record->sportInfo->jersey_number ?? null,
            'height_cm' => $this->record->sportInfo->height_cm ?? null,
            'weight_kg' => $this->record->sportInfo->weight_kg ?? null,
        ];

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->person->update([
            'first_name' => $data['person']['first_name'],
            'last_name' => $data['person']['last_name'],
            'usual_name' => trim($data['person']['first_name'] . ' ' . $data['person']['last_name']),
            'birth_date' => $data['person']['birth_date'] ?? null,
            'gender' => $data['person']['gender'] ?? null,
            'nationality_id' => $data['person']['nationality_id'] ?? null,
            'updated_by' => auth()->id(),
        ]);

        $record->update([
            'fifa_connect_id' => $data['fifa_connect_id'] ?? null,
            'fifa_id' => $data['fifa_id'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            'updated_by' => auth()->id(),
        ]);

        $record->sportInfo()->updateOrCreate([
            'player_id' => $record->id,
        ], [
            'position_id' => $data['sportInfo']['position_id'] ?? null,
            'jersey_name' => $data['sportInfo']['jersey_name'] ?? null,
            'jersey_number' => $data['sportInfo']['jersey_number'] ?? null,
            'height_cm' => $data['sportInfo']['height_cm'] ?? null,
            'weight_kg' => $data['sportInfo']['weight_kg'] ?? null,
            'created_by' => $record->sportInfo ? $record->sportInfo->created_by : auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
