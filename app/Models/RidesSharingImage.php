<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RidesSharingImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'rides_sharing_id',
        'rides_sharing_name',
        'vehicule_name',
        'image_path',
    ];

    public function ridesSharing()
    {
        return $this->belongsTo(RidesSharing::class, 'rides_sharing_id');
    }
}
