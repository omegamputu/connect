<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlayerPosition extends Model
{
    //
    use SoftDeletes;
    protected $fillable = [
        'name', 
        'code',
        'created_by',
        'updated_by',
    ];
}
