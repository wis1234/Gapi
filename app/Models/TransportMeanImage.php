<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportMeanImage extends Model
{
    use HasFactory;

    protected $fillable = ['transport_mean_id', 'transport_mean_name', 'image_path'];

    public function transportMean()
    {
        return $this->belongsTo(TransportMean::class);
    }
}
