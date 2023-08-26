<?php

namespace App\Models;

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaterSum extends Model
{
    use HasFactory;

    protected $table = 'cater_sum';

    protected $fillable = [
        'catering_service_name',
        'total_cost'

    ];

    // Define any other properties, relationships, or methods here
}
