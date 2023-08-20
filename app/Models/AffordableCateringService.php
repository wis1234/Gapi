<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffordableCateringService extends Model
{
    protected $table = 'affordable_catering_services';

    protected $fillable = [
        'catering_service_id',
        'total_cost',
    ];

    public function cateringService()
    {
        return $this->belongsTo(CateringService::class);
    }

    public function calculateTotalCost()
    {
        $totalCost = $this->cateringService->aperitifs()->sum('cost')
                   + $this->cateringService->appetizers()->sum('cost')
                   + $this->cateringService->mainDishes()->sum('cost')
                   + $this->cateringService->desserts()->sum('cost');

        return $totalCost;
    }
}
