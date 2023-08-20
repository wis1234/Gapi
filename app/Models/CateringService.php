<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CateringService extends Model
{
    protected $table = 'catering_services';
    protected $fillable = [
        'name',
        'num_member',
        'num_girl',
        'num_boy',
        'address',
        'ifu',
        'image',
        'manager_firstname', 
        'manager_lastname',
    'manager_phone', 
    'manager_email',
        'catering_service_code',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function aperitifs()
    {
        return $this->hasMany(Aperitif::class, 'catering_service_id');
    }

    public function appetizers()
    {
        return $this->hasMany(Appetizer::class);
    }

    public function mainDishes()
    {
        return $this->hasMany(MainDish::class);
    }

    public function desserts()
    {
        return $this->hasMany(Dessert::class);
    }

    public function affordableCateringService()
    {
        return $this->hasOne(AffordableCateringService::class);
    }

    public function answer()
    {
        return $this->hasMany(Answer::class);
    }
}
