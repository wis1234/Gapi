<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    use HasFactory;

    protected $table='courier';
    protected $fillable = ['user_id', 'role','photo'];
    

    public function user()
{
    return $this->belongsTo(User::class);
}
}
