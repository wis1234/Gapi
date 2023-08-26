<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainDish extends Model
{
    protected $table = 'main_dish';

    protected $fillable = [
        'name',
        'description',
        'num_guest',
        'cost',
        'image1',
        'catering_service_name',
        'catering_service_id',
    ];

    protected $casts = [
        'image1' => 'array',
    ];

    public function cateringService()
    {
        return $this->belongsTo(CateringService::class);
    }
}
