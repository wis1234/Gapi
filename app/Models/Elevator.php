<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Elevator extends Model
{
    use HasFactory;

    protected $table = 'elevators';

    protected $fillable = ['elevator_type', 'num_elevator', 'house_id'];

    // Define relationships
    public function house()
    {
        return $this->belongsTo(House::class);
    }
}
