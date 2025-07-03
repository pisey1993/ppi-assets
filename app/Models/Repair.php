<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Repair extends Model
{
    protected $table = 'asset_repairs';

    protected $fillable = [
        'asset_id',
        'repair_date',
        'issue',
        'repair_cost',
        'status',
        'vendor',
        'remarks',
        'updated_at',
        'created_at',
    ];

    public function asset()
    {
        return $this->belongsTo(Assets::class, 'asset_id');
    }
}
