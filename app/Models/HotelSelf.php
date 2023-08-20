<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelSelf extends Model
{
    use HasFactory;

    protected $table = 'hotel_self';

    protected $fillable = [
        'name', 'address', 'city', 'manager_firstname', 'manager_lastname', 'manager_phone',
        'manager_email', 'hotel_code', 'website', 'user_id'
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
}
