<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id', 'event_name', 'image_path',
    ];
    protected $hidden = ['event_id', 'event_name'];

    // Define the relationship with Event model
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
