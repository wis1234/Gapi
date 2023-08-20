<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiculeTable extends Migration
{
    public function up()
    {
        Schema::create('vehicules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->text('description');
            $table->string('Lone_delay')->nullable();
            $table->decimal('price', 10, 2);
            $table->boolean('availability');
            $table->unsignedBigInteger('rides_sharing_id')->nullable();
            $table->string('rides_sharing_name')->nullable();
            $table->timestamps();
    
            $table->foreign('rides_sharing_id')->references('id')->on('rides_sharing')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('vehicule');
    }
}
