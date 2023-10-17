<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    use HasFactory;

    protected $table='courier';
    protected $fillable = ['user_id', 'role','courier_photo','photo',
    'courier_firstname', 'courier_lastname', 'courier_phone',
    'courier_email', 'courier_photo', 'tm_type','description',  ];
    
protected $hidden =['courier_photo', 'courier_firstname', 'courier_lastname', 'courier_phone','photo','courier_email'];
    public function user()
{
    return $this->belongsTo(User::class);
}


}
