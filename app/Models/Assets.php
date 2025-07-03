<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assets extends Model
{
    protected $table = 'assets'; // or whatever table you're accessing

    protected $fillable = [
        'asset_code',
        'name',
        'category',
        'model',
        'serial_number',
        'vendor',
        'purchase_date',
        'purchase_cost',
        'warranty_expiry',
        'status',
        'current_location_id',
        'assigned_to_user_id',
        'notes',
    ];

    public function transfers()
    {
        return $this->hasMany(AssetTransfer::class, 'asset_id');
    }

    public function repairs()
    {
        return $this->hasMany(Repair::class, 'asset_id');
    }
    public function category()
    {
        return $this->belongsTo(Categories::class, 'category'); // adjust column if it's named differently
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }
    public function currentLocation()
    {
        return $this->belongsTo(Locations::class, 'current_location_id');
    }



}
