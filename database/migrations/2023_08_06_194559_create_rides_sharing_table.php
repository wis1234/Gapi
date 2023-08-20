<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRidesSharingTable extends Migration
{
    public function up()
    {
        Schema::create('rides_sharing', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('city');
            $table->string('website')->nullable();
            $table->string('image')->nullable();
            $table->string('manager_firstname');
            $table->string('manager_lastname');
            $table->string('manager_phone');
            $table->string('manager_email');
            $table->string('rides_sharing_code');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('rides_sharing');
    }
}
