<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transport_mean', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->string('description');
            $table->string('departure_country');
            $table->string('departure_city');
            $table->date('departure_date');
            $table->time('departure_time');
            $table->string('destination_country');
            $table->string('destination_city');
            $table->date('destination_date');
            $table->time('destination_time');
            $table->string('price');
            $table->string('availability');
            $table->string('travel_agency_name');
            $table->string('travel_agency_id')->constrained('travel_agencies');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_mean');
    }
};
