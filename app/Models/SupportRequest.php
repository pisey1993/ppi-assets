<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportRequest extends Model
{
    protected $connection = 'mysql2';
    protected $table = 'users'; // or whatever table you're accessing
}
