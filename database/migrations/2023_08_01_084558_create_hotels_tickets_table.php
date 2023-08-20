<?php

// database/migrations/YYYY_MM_DD_create_hotel_tickets_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHotelsTicketsTable extends Migration
{
    public function up()
    {
        Schema::create('hotels_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels');
            $table->string('name');
            $table->string('type');
            $table->string('price');
            $table->integer('total');
            $table->integer('solded')->default(0);
            $table->integer('available')->default(0);
            $table->foreignId('buyer_id')->nullable()->constrained('users');
            $table->date('purchase_date')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hotels_tickets');
    }
}
