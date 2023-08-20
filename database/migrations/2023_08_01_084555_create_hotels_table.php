<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHotelsTable extends Migration
{
    public function up()
    {
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
             $table->string('state');  
            $table->integer('num_roomavail');
            $table->string('room_type');
            $table->string('room_price');
            $table->string('description');
            // $table->string('phone');
            // $table->string('email');
            // $table->string('password');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->rememberToken();


            

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hotels');
    }
}
