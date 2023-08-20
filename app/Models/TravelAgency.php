<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelAgency extends Model
{
    use HasFactory;
    
    protected $table= 'travel_agencies';

    protected $fillable = [
        'name',
        'address',
        'city',
        'website',
        'travel_agency_code',
        'manager_firstname',
        'manager_lastname',
        'manager_email',
        'manager_phone',
        'image',
        'user_id',
    ];

    /**
     * Get the user that owns the travel agency.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function transport_mean()
    {
        return $this->hasMany(TransportMean::class, 'travel_agency_id');
    }
}
