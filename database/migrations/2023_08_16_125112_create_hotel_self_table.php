<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHotelSelfTable extends Migration
{
    public function up()
    {
        Schema::create('hotel_self', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('low_price')->nullable();
            $table->string('address');
            $table->string('city');
            $table->string('manager_firstname');
            $table->string('manager_lastname');
            $table->string('manager_phone');
            $table->string('manager_email');
            $table->string('hotel_self_images')->nullable();
            $table->string('hotel_code')->unique();
            $table->string('website')->nullable();
            $table->unsignedBigInteger('hotel_id')->nullable(); // Make it nullable
            $table->foreign('hotel_id')->references('id')->on('hotels')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hotel_self');
    }
}
