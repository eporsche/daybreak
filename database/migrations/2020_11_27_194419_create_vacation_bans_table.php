<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVacationBansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vacation_bans', function (Blueprint $table) {
            $table->foreignId('location_id')->constrained();
            $table->id();
            $table->timestamps();

            $table->string('title');
            $table->date('starts_at');
            $table->date('ends_at');
            $table->foreignId('absence_type_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vacation_bans');
    }
}
