<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ImmoAgence extends Model
{
    protected $table = 'immo_agences';

    protected $fillable = [
        'name',
        'address',
        'city',
        'manager_firstname',
        'manager_lastname',
        'manager_phone',
        'manager_email',
        'image',
        'immo_agence_code',
        'website',
        'user_id',
    ];
   // Define the relationship with the User model (belongs to a User)
   public function user()
   {
       return $this->belongsTo(User::class,'user_id');
   }

    // Define relationships
    public function houses()
    {
        return $this->hasMany(House::class, 'immo_agence_id');
    }

    public function apartments()
    {
        return $this->hasMany(Apartment::class, 'immo_agence_id');
    }
}
