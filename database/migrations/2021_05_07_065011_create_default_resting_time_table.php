<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDefaultRestingTimeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('time_trackings', function (Blueprint $table) {
            $table->dropColumn('manual_pause');
            $table->integer('pause_time')->nullable(); //in seconds
        });

        Schema::create('default_resting_times', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('location_id')->constrained()->cascadeOnDelete();
            $table->integer('min_hours'); //in seconds
            $table->integer('duration'); //in seconds
        });

        Schema::create('default_resting_time_users', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('default_resting_time_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
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
            $table->integer('manual_pause');
            $table->dropColumn('pause_time');
        });

        Schema::dropIfExists('default_resting_time_users');

        Schema::dropIfExists('default_resting_times');
    }
}
