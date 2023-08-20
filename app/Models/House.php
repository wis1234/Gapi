<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class House extends Model
{
    protected $table = 'houses';

    protected $fillable = [
        'name', 'address', 'city', 'state', 'country'
        , 'num_bedrooms', 'num_bathrooms', 'num_livingrooms', 
        'num_apartments', 'price','type', 'apartments_type', 'property_type',
         'area','image1', 'image2', 'image3', 'description', 'immo_agence_name', 'immo_agence_id',
         'notarial_information'
    ];

    // Define relationships
    public function immoAgence()
    {
        return $this->belongsTo(ImmoAgence::class, 'immo_agence_id');
    }

    public function elevator()
    {
        return $this->hasOne(Elevator::class);
    }
}
