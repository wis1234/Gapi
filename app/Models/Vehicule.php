<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicule extends Model
{
    use HasFactory;
    protected $table = 'vehicule';

    protected $fillable = [
        'name',
        'type',
        'description',
        'Lone_delay',
        'price',
        'availability',
        'rides_sharing_id', // Assuming you have a foreign key column for rides_sharing_id
        'rides_sharing_name', // Assuming you have a column to store the rides_sharing_name
    ];

    public function ridesSharing()
    {
        return $this->belongsTo(RidesSharing::class, 'rides_sharing_id');
    }

    public function ridesSharingImages()
    {
        return $this->hasMany(RidesSharingImage::class, 'rides_sharing_id', 'rides_sharing_id');
    }
    public function getImagesAttribute()
{
    return $this->ridesSharingImages->pluck('image_path')->toArray();
}
}
