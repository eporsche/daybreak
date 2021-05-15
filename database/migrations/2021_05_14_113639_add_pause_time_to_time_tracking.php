<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPauseTimeToTimeTracking extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('time_trackings', function (Blueprint $table) {
            $table->integer('pause_time')->nullable(); //in seconds
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('time_trackings', function (Blueprint $table) {
            $table->dropColumn('pause_time');
        });
    }
}
