<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    protected $table = 'categories'; // or whatever table you're accessing

    protected $fillable = [
        'category_name',
    ];
}
