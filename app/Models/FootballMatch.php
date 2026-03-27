<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FootballMatch extends Model
{
    //
    use SoftDeletes;

    protected $fillable = [
        'competition_id',
        'category_id',
        'season_id',
        'home_team_id',
        'away_team_id',
        'venue',
        'city',
        'country',
        'match_number',
        'external_reference',
        'match_date',
        'kickoff_time',
        'status',
        'stage',
        'leg',
        'home_score',
        'away_score',
        'home_score_ht',
        'away_score_ht',
        'is_neutral_venue',
        'is_international',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'match_date' => 'date',
        'is_neutral_venue' => 'boolean',
        'is_international' => 'boolean',
    ];

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
