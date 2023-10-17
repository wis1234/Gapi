<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    protected $table = 'restaurants';

    protected $fillable = ['name', 'address', 'city', 'image','website', 'manager_firstname', 'manager_lastname',
    'manager_phone', 'manager_email', 'restaurant_code', 'description', 'open_time', 'close_time' ,'user_id'];

    // Define the relationship between Restaurant and User models
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    //menu prices
    public function menuprices()
    {
        return $this->hasMany(MenuPrice::class, 'restaurant_id');
    }

    //menu
    public function menu()
    {
        return $this->hasMany(Menu::class, 'restaurant_id');
    }
     //drink
     public function drink()
     {
         return $this->hasMany(Menu::class, 'restaurant_id');
     }
 
    //customer_demand
    public function customerDemands()
    {
        return $this->hasMany(CustomerDemand::class);
    }

    public function menus()
{
    return $this->hasMany(Menu::class);
}

}
