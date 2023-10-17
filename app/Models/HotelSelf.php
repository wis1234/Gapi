<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelSelf extends Model
{
    use HasFactory;

    protected $table = 'hotel_self';

    protected $fillable = [
        'name', 'address', 'latitude', 'longitude', 'city', 'manager_firstname', 'manager_lastname', 'manager_phone', 'hotel_self_images', 'low_price',
        'manager_email', 'hotel_code','hotel_name', 'website', 'user_id'
    ];

    // Define the relationship with the User model (belongs to a User)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

        // Define the relationship with the HotelImage model (has many images)
        public function images()
        {
            return $this->hasMany(HotelImage::class);
        }


        // hidden items does here
// protected $hidden = ['created_at', 'updated_at','low_price'];


    // Define the inverse of the relationship: each HotelSelf belongs to a Hotel
    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

}
