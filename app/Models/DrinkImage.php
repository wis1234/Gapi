<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrinkImage extends Model


{
    protected $table = 'drink_image_new';
    protected $fillable = ['drink_id', 'drink_name', 'image_path'];

    public function drink()
    {
        return $this->belongsTo(Drink::class, 'drink_id');
    }
}
