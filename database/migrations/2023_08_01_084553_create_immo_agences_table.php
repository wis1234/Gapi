<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImmoAgencesTable extends Migration
{
    public function up()
    {
        Schema::create('immo_agences', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('city');
            $table->string('manager_firstname');
            $table->string('manager_lastname');
            $table->string('manager_phone');
            $table->string('manager_email');
            $table->string('image')->nullable();
            $table->string('immo_agence_code');
            $table->string('website');
            // $table->string('password');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('immo_agences');
    }
}
