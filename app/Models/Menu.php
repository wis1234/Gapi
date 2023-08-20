<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;
    
    protected $table = 'menu';

    protected $fillable = ['name', 'price', 'availability', 'restaurant_name', 'restaurant_id'];

    // Define the relationship between MenuPrice and Restaurant models
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    public function menuImages()
{
    return $this->hasMany(MenuImage::class);
}

}
