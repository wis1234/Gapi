<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCateringServiceClientTable extends Migration
{
    public function up()
    {
        Schema::create('catering_service_client', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('aperitif_name');
            $table->string('appetizer_name');
            $table->string('main_dish_name');
            $table->string('dessert_name');
            $table->integer('num_guest');
            $table->decimal('budget', 10, 2);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('catering_service_client');
    }
}
