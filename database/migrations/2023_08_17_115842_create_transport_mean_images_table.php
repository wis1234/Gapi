<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransportMeanImagesTable extends Migration
{
    public function up()
    {
        Schema::create('transport_mean_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transport_mean_id');
            $table->string('transport_mean_name');
            $table->string('image_path');
            $table->timestamps();

            $table->foreign('transport_mean_id')
                ->references('id')
                ->on('transport_mean')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('transport_mean_images');
    }
}

