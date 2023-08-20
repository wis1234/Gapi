<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerDemandsTable extends Migration
{
    public function up()
    {
        Schema::create('customer_demands', function (Blueprint $table) {
            $table->id();
            $table->string('dish_name');
            $table->string('drink_name')->nullable();
            $table->unsignedInteger('num_dish');
            $table->unsignedInteger('num_drink');
            $table->string('restaurant_name');
            $table->unsignedBigInteger('restaurant_id');
            $table->string('option');
            $table->timestamps();

            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('customer_demands');
    }
}
