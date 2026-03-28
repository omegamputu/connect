<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Club extends Model
{
    //
    use SoftDeletes;

    protected $fillable = [
        'name',
        'country_id',
        'created_by',
        'updated_by',
    ];
}
