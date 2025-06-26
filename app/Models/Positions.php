<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Positions extends Model
{
    protected $table = 'positions'; // or whatever table you're accessing

    protected $fillable = [
        'id',
        'positions_name',
        'created_at',
        'updated_at',
    ];
}

