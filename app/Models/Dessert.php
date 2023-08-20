<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dessert extends Model
{
    protected $table = 'dessert';

    protected $fillable = [
        'name',
        'description',
        'num_guest',
        'cost',
        'image1',
        'image2',
        'image3', 
        'catering_service_name',
        'catering_service_id',
    ];

    public function cateringService()
    {
        return $this->belongsTo(CateringService::class);
    }
}

