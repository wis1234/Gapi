<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'restaurant_name',
        'image_path',
    ];

    // Define the inverse relationship to the Menu model
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
