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
            $table->unsignedBigInteger('user_id')->nullable();
             $table->string('state')->nullable();  
            $table->integer('num_roomavail')->nullable();
            $table->string('room_type');
            $table->string('hotel_name');
            $table->string('hotel_address');
            $table->string('room_price');
            $table->string('description');
            
            // $table->string('email');
            $table->string('image_path')->nullable();
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
