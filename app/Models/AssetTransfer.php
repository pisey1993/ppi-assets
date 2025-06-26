<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetTransfer extends Model
{
    protected $table = 'asset_transfers';

    protected $fillable = [
        'asset_id',
        'from_user_id',
        'to_user_id',
        'from_location',
        'to_location',
        'transfer_date',
        'approved_by',
        'reason',
        'user_status',
    ];

    public $timestamps = true;

    public function asset()
    {
        return $this->belongsTo(Assets::class);
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    // Add this alias relationship:
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function fromLocation()
    {
        return $this->belongsTo(Locations::class, 'from_location', 'id');
    }

    public function toLocation()
    {
        return $this->belongsTo(Locations::class, 'to_location', 'id');
    }
}
