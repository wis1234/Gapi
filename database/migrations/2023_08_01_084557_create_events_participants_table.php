<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvEntsParticipantsTable extends Migration
{
    public function up()
    {
        Schema::create('events_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('event_code');
            // ->constrained('events');
            $table->string('opinion');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('events_participants');
    }
}
