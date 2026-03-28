<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Person extends Model
{
    //
    use SoftDeletes;

    protected $table = 'people';

    protected $fillable = [
        'first_name',
        'last_name',
        'usual_name',
        'birth_date',
        'gender',
        'nationality_id',
        'passport_number',
        'is_active',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_active' => 'boolean'
    ];

    public function player(): HasOne
    {
        return $this->hasOne(Player::class);
    }
}
