<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $table = 'events'; // Specify the table name

    protected $fillable = [
        'name', 'type', 'description', 'latitude', 'longitude', 'date', 'time', 'place',
        'creator_firstname', 'creator_lastname', 'appreciation', 'image', 'total_seat', 'remain_seat', 'creator_id', 'event_code'
    ];
    protected $hidden  = ['image'];


    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id','id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(EventParticipant::class, 'event_id');
    }

    // Other relationships can be defined here...

    public function images()
    {
        return $this->hasMany(EventImage::class);
    }
}

