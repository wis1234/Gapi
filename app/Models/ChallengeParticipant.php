<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChallengeParticipant extends Model
{
    protected $table = 'challenges_participants';

    
    protected $fillable = [
        'user_id',
        'event_id',
        'challenge_id',
        'event_name',
        'participant_firstname',
        'participant_lastname',
        'challenge_name',
        'appreciation',
        'result',
        'opinion',
    ];
    // Define relationships with other models here
    // For example, if ChAllengeParticipant belongs to a User:
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // If ChAllengeParticipant belongs to an EvEnt:
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    // If ChAllengeParticipant belongs to a ChAllenge:
    public function challenge()
    {
        return $this->belongsTo(Challenge::class, 'challenge_id');
    }
}

