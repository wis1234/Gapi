<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportMean extends Model
{
    use HasFactory;

    protected $table = 'transport_mean';

    protected $fillable = [
        'name',
        'type',
        'description',
        'departure_country',
        'departure_city',
        'departure_date',
        'departure_time',
        'destination_country',
        'destination_city',
        'destination_date',
        'destination_time',
        'price',
        'availability',
        'travel_agency_name',
        'travel_agency_id',
    ];

    protected $casts = [
        'departure_date' => 'date',
        'departure_time' => 'datetime:H:i:s',
        'destination_date' => 'date',
        'destination_time' => 'datetime:H:i:s',
        'price' => 'decimal:2', // Format price as a decimal with 2 decimal places
    ];

    public function travelAgency()
    {
        return $this->belongsTo(TravelAgency::class);
    }

    public function transportMeanImages()
{
    return $this->hasMany(TransportMeanImage::class);
}

}
