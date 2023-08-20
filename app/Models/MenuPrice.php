<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuPrice extends Model
{
    protected $table = 'menu_prices';

    protected $fillable = ['cost', 'restaurant_id'];

    // Define the relationship between MenuPrice and Restaurant models
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }
}
