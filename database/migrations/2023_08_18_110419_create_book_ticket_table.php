<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookTicketTable extends Migration
{
    public function up()
    {
        Schema::create('book_ticket', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_type');
            $table->string('ticket_name');
            $table->string('event_name');
            $table->string('ticket_code');
            $table->decimal('price', 10, 2);
            $table->date('validity');
            $table->string('secret_key');
            $table->string('owner_firstname');
            $table->string('owner_lastname');
            $table->string('owner_phone');
            $table->string('owner_email');
            $table->string('owner_photo');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('book_ticket');
    }
}
