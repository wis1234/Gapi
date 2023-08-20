<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerDemand extends Model
{
    protected $fillable = ['dish_name', 'drink_name', 'num_dish', 'num_drink', 'restaurant_name', 'restaurant_id', 'option'];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
