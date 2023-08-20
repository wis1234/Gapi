<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'sum',
        'catering_service_id',
    ];

    public function cateringService()
    {
        return $this->belongsTo(CateringService::class);
    }

    // Define any additional methods or logic here

    public function updateSum()
    {
        $sum = 0;

        // Calculate the sum based on related records in other tables
        $cateringService = $this->cateringService;
        $sum += $cateringService->aperitifs->sum('cost');
        $sum += $cateringService->appetizers->sum('cost');
        $sum += $cateringService->mainDishes->sum('cost');
        $sum += $cateringService->desserts->sum('cost');

        // Update the sum field
        $this->update(['sum' => $sum]);
    }
}
