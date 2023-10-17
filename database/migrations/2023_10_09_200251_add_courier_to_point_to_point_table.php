<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCourierToPointToPointTable extends Migration
{
    public function up()
    {
        Schema::table('point_to_point', function (Blueprint $table) {
            $table->string('courier')->nullable();
        });
    }

    public function down()
    {
        Schema::table('point_to_point', function (Blueprint $table) {
            $table->dropColumn('courier');
        });
    }
}
