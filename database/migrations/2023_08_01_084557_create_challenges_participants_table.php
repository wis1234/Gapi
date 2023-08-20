<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChAllengesParticipantsTable extends Migration
{
    public function up()
    {
        Schema::create('challenges_participants', function (Blueprint $table) {
            $table->id();
            
            // Foreign key references
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('challenge_id');
            
            // Participant and challenge details
            $table->string('event_name');
            $table->string('participant_firstname');
            $table->string('participant_lastname');
            $table->string('challenge_name');
            
            // Additional data
            $table->string('appreciation')->nullable();
            $table->string('result')->nullable();
            $table->string('opinion')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('challenge_id')->references('id')->on('challenges')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('challenges_participants');
    }
}
