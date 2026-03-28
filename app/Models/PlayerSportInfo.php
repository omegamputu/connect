<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlayerSportInfo extends Model
{
    //
    use SoftDeletes;

    protected $fillable = [
        'player_id',
        'position_id',
        'jersey_name',
        'jersey_number',
        'height_cm',
        'weight_kg',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(PlayerPosition::class);
    }
}
