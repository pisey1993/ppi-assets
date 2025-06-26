<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employees extends Model
{
    protected $connection = 'mysql2';
    protected $table = 'users'; // or whatever table you're accessing

    protected $fillable = [
        'name',
        'email',
        'password',
        'position',
        'department_id',
        'view_check_request',
    ];

}
