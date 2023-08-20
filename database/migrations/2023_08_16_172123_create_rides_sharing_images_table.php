<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('rides_sharing_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rides_sharing_id');
            $table->string('rides_sharing_name');
            $table->string('vehicule_name');
            $table->string('image_path');
            $table->timestamps();
    
            $table->foreign('rides_sharing_id')->references('id')->on('rides_sharing')->onDelete('cascade');
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rides_sharing_images');
    }
};
