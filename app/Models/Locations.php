<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Locations extends Model
{
    protected $table = 'locations'; // or whatever table you're accessing

    protected $fillable = [
        'name',
    ];
}
