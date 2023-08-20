<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrinkImageNewTable extends Migration
{
    public function up()
    {
        Schema::create('drink_image_new', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('drink_id');
            $table->string('drink_name');
            $table->string('image_path');
            $table->timestamps();

            // Define foreign key constraint
            $table->foreign('drink_id')->references('id')->on('drink')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('drink_image_new');
    }
}
