<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RidesSharing extends Model
{
    protected $table = 'rides_sharing';

    protected $fillable = ['name', 'address', 'city', 'website', 'image', 'manager_firstname', 'manager_lastname', 'manager_phone', 'manager_email', 'rides_sharing_code', 'user_id'];

    // Define the relationship between RidesSharing and User models
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function vehicule()
    {
        return $this->hasMany(Vehicule::class, 'rides_sharing_id');

    }
}
