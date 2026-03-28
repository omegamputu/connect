<?php

namespace Database\Seeders;

use App\Models\PlayerPosition;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlayerPositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        PlayerPosition::insert([
            ['name' => 'Goalkeeper', 'code' => 'GK'],
            ['name' => 'Defender', 'code' => 'DF'],
            ['name' => 'Midfielder', 'code' => 'MF'],
            ['name' => 'Forward', 'code' => 'FW'],
        ]);
    }
}
