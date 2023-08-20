<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAffordableCateringServicesTable extends Migration
{
    public function up()
    {
        Schema::create('affordable_catering_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('catering_service_id');
            $table->decimal('total_cost', 10, 2);
            $table->timestamps();

            $table->foreign('catering_service_id')->references('id')->on('catering_services')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('affordable_catering_services');
    }
}
