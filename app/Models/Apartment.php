<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    protected $table = 'apartments'; // Specify the correct table name here

    protected $fillable = ['type', 'address', 'num_bedrooms', 'num_bathrooms', 'num_livingrooms', 'description', 'image1', 'image2','image3', 'price', 'notarial_information', 'immo_agence_id','immo_agence_name'];

    public function immoAgence()
    {
        return $this->belongsTo(ImmoAgence::class, 'immo_agence_id');
    }
}
