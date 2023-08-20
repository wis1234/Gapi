<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CateringServiceClient extends Model
{
    protected $table = 'catering_service_client';

    protected $fillable = [
        'user_id',
        'aperitif_name',
        'appetizer_name',
        'main_dish_name',
        'dessert_name',
        'num_guest',
        'budget',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
