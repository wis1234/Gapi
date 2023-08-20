<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChAllengesTable extends Migration
{
    public function up()
    {
        Schema::create('challenges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('delay');
            $table->string('awards');
            $table->date('starts_at');
            $table->date('ends_at');
            $table->integer('appreciation');
            $table->string('challenge_code')->unique();
            $table->foreignId('event_id')->constrained('events');
            $table->string('event_name');
            $table->string('creator_firstname');
            $table->string('creator_lastname');
            $table->foreignId('creator_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('challenges');
    }
}
