<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHousesTable extends Migration
{
    public function up()
    {
        Schema::create('houses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('city');
            $table->string('state');
            $table->string('country');
            $table->integer('num_bedrooms');
            $table->integer('num_bathrooms');
            $table->integer('num_livingrooms');
            $table->integer('num_apartments');
            $table->string('price');
            $table->string('type');    
            $table->string('apartments_type');        
            $table->string('property_type');
            $table->float('area');
            $table->string('image1');
            $table->string('image2');
            $table->string('image3');
            $table->string('description');
            $table->string('immo_agence_name');
            $table->text('notarial_information')->nullable(); 
            $table->foreignId('immo_agence_id')->constrained('immo_agences', 'id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('houses');
    }
}

