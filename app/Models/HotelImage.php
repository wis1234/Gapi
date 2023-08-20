<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'hotel_name',
        'image_path', // Add 'image_path' to the fillable array

    ];

    public function hotel()
{
    return $this->belongsTo(HotelSelf::class);
}

}
