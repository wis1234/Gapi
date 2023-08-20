<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvEntsTable extends Migration
{
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->text('description');
            $table->date('date');
            $table->time('time');
            $table->string('place');
            $table->string('creator_firstname');
            $table->string('creator_lastname');
            $table->string('appreciation')->nullable();
            $table->string('image')->nullable();

            $table->integer('total_seat');
            $table->integer('remain_seat');
            $table->foreignId('creator_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('events');
    }
}

