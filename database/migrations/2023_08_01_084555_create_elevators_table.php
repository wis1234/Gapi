<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateElevatorsTable extends Migration
{
    public function up()
    {
        Schema::create('elevators', function (Blueprint $table) {
            $table->id();
            $table->string('elevator_type');
            $table->integer('num_elevator');
            $table->unsignedBigInteger('house_id');
            $table->foreign('house_id')->references('id')->on('houses');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('elevators');
    }
}
