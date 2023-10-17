<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointToPoint extends Model
{
    use HasFactory;

    protected $table = 'point_to_point';

    protected $fillable = [
        'title',
        'sender_address',
        'sender_phone',
        'receiver_address',
        'receiver_phone',
        'description',
        'courier'
    ];
}
