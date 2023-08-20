<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAperitifTable extends Migration
{
    public function up()
    {
        Schema::create('aperitif', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->integer('num_guest');
            $table->decimal('cost', 10, 2);
            $table->string('image1')->nullable();
            $table->string('image2')->nullable();
            $table->string('image3')->nullable();
            $table->string('catering_service_name');
            $table->unsignedBigInteger('catering_service_id');
            $table->timestamps();

            $table->foreign('catering_service_id')->references('id')->on('catering_services')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('aperitif');
    }
}

