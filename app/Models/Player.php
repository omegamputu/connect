<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Player extends Model
{
    //
    use SoftDeletes;

    protected $fillable = [
        'person_id',
        'fifa_connect_id',
        'fifa_id',
        'is_active',
        'created_by',
        'updated_by',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function sportInfo(): HasOne
    {
        return $this->hasOne(PlayerSportInfo::class);
    }

    public function clubRegistrations(): HasMany
    {
        return $this->hasMany(PlayerClubRegistration::class);
    }

    public function currentClubRegistration(): HasOne
    {
        return $this->hasOne(PlayerClubRegistration::class)->where('is_current', true);
    }
}
