<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'users';
    protected $fillable = [
        'firstname',
        'lastname',
        'age',
        'gender',
        'phone',
        'photo',
        'email',
        'account_type',
        'google_id',
        'phone_code',
        'secret_key',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // jwt method implementation

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    
    // Define the relationship with CateringService model (a user can have many CateringServices)
    public function cateringServices()
    {
        return $this->hasMany(CateringService::class, 'user_id');
    }

    // Define the relationship with Hotel model (a user can have many Hotels)
    public function hotels()
    {
        return $this->hasMany(Hotel::class, 'user_id');
    }

    // Define the relationship with ImmoAgence model (a user can have many ImmoAgences)
    public function immoAgences()
    {
        return $this->hasMany(ImmoAgence::class, 'user_id');
    }

     // Define the relationship with Event model (a user can have many events as creator)
     public function events()
     {
         return $this->hasMany(Event::class, 'creator_id');
     }
     //challenges
    public function challenges()
    {
        return $this->hasMany(Challenge::class, 'user_id');
    }
   
    //events participants
    public function eventparticipant()
    {
        return $this->hasMany(EventParticipant::class, 'user_id');
    }
    //challenges participants
    
    public function challengeparticipant()
    {
        return $this->hasMany(ChallengeParticipant::class, 'user_id');
    }
    //travel agencies
    public function travelagence()
    {
        return $this->hasMany(TravelAgency::class, 'user_id');
    }
     //rides sharings
     public function ridessharing()
     {
         return $this->hasMany(RidesSharing::class, 'user_id');
     }
       //restaurant
       public function restaurant()
       {
           return $this->hasMany(Restaurant::class, 'user_id');
       }
   //catering services client relationship
 
       public function cateringServiceClients()
       {
           return $this->hasMany(CateringServiceClient::class);
       }
       //courier table relationship
       
       public function couriers()
       {
           return $this->hasMany(Courier::class);
       }
       public function bookedTickets()
    {
        return $this->hasMany(BookTicket::class, 'owner_email', 'email');
    }



    // ppic handling and storing
    public function getProfileImageUrlAttribute()
{
    if ($this->photo) {
        return asset("storage/uploads/profiles/{$this->photo}");
    }

    // Retourner une URL par défaut ou vide si l'utilisateur n'a pas de photo de profil
    return asset("path_vers_votre_image_par_defaut.jpg");
}


}


