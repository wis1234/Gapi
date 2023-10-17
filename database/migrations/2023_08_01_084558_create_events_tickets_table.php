<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvEntsTicketsTable extends Migration
{
    public function up()
    {
        Schema::create('events_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->integer('price');
            $table->integer('total')->nullable();
            $table->integer('solded')->nullable();
            $table->integer('available')->nullable();
            $table->string('event_name');
            $table->string('creator_firstname');
            $table->string('creator_lastname');

            $table->date('expiration_date')->nullable();
            $table->string('ticket_n°');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('events_tickets');
    }
}


// the trigger 

// DELIMITER //
// CREATE TRIGGER set_available_value
// BEFORE INSERT ON ev_ents_tickets
// FOR EACH ROW
// BEGIN
//     SET NEW.available = NEW.total - NEW.solded;
// END;
// //
// DELIMITER ;

