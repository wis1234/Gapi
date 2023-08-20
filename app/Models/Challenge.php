<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Challenge extends Model
{
    use HasFactory;
    protected $table = 'challenges';

    protected $fillable = [
        'name',
        'description',
        'delay',
        'awards',
        'starts_at',
        'ends_at',
        'appreciation',
        'challenge_code',
        'event_id',
        'event_name',
        'creator_firstname',
        'creator_lastname',
        'creator_id',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function participants()
    {
        return $this->hasMany(ChallengeParticipant::class, 'challenge_id');
    }
}
