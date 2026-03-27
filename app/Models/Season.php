<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Season extends Model
{
    use SoftDeletes;
    //
    protected $fillable = [
        'name', 
        'code',
        'description',
        'start_date',
        'end_date',
        'is_current',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_current' => 'boolean',
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function competitions(): HasMany
    {
        return $this->hasMany(Competition::class);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function matches(): HasMany
    {
        return $this->hasMany(FootballMatch::class);
    }
}
