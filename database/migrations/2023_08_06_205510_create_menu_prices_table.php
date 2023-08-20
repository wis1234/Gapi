<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuPricesTable extends Migration
{
    public function up()
    {
        Schema::create('menu_prices', function (Blueprint $table) {
            $table->id();
            $table->string('cost');
            $table->unsignedBigInteger('restaurant_id');
            $table->timestamps();

            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('menu_prices');
    }
}
