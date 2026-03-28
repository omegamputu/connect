<?php

namespace App\Filament\Resources\Players\Pages;

use App\Filament\Resources\Players\PlayerResource;
use App\Models\Person;
use App\Models\Player;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreatePlayer extends CreateRecord
{
    protected static string $resource = PlayerResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $person = Person::create([
            'first_name' => $data['person']['first_name'],
            'last_name' => $data['person']['last_name'],
            'usual_name' => trim($data['person']['first_name'] . ' ' . $data['person']['last_name']),
            'birth_date' => $data['person']['birth_date'] ?? null,
            'gender' => $data['person']['gender'] ?? null,
            'nationality_id' => $data['person']['nationality_id'] ?? null,
            'is_active' => true,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        $player = Player::create([
            'person_id' => $person->id,
            'fifa_connect_id' => $data['fifa_connect_id'] ?? null,
            'fifa_id' => $data['fifa_id'] ?? null,
            'is_active' => true,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        $player->sportInfo()->create([
            'player_id' => $player->id,
            'position_id' => $data['sportInfo']['position_id'] ?? null,
            'jersey_name' => $data['sportInfo']['jersey_name'] ?? null,
            'jersey_number' => $data['sportInfo']['jersey_number'] ?? null,
            'height_cm' => $data['sportInfo']['height_cm'] ?? null,
            'weight_kg' => $data['sportInfo']['weight_kg'] ?? null,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return $player;
    }
}
