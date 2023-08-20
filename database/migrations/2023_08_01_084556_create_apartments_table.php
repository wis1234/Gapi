<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApartmentsTable extends Migration
{
    public function up()
    {
        Schema::create('apartments', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('address');
            $table->integer('num_bedrooms');
            $table->integer('num_bathrooms');
            $table->string('num_livingrooms'); 
            $table->string('description'); 
            $table->string('image1'); 
            $table->string('image2'); 
            $table->string('image3'); 
            $table->string('price'); 
            $table->text('notarial_information')->nullable(); 
            $table->string('immo_agence_name'); 
            $table->unsignedBigInteger('immo_agence_id');
            $table->timestamps();

            $table->foreign('immo_agence_id')->references('id')->on('immo_agences')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('apartments');
    }
}
