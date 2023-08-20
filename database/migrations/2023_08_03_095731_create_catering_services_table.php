<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCateringServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catering_services', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->integer('num_member');
            $table->integer('num_girl');
            $table->integer('num_boy');
            $table->string('address', 255);
            $table->string('ifu', 255);
            $table->string('image')->nullable();
            $table->string('manager_firstname');
            $table->string('manager_lastname');
            $table->string('manager_email');
            $table->string('manager_phone');

            $table->string('catering_service_code');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('catering_services');
    }
}
