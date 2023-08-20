<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Drink extends Model
{
    use HasFactory;
    protected $table = 'drink';

    protected $fillable = [
        'name',
        'price',
        'availability',
        'restaurant_id',
        'restaurant_name',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function images()
    {
        return $this->hasMany(DrinkImage::class, 'drink_id');
    }
}
