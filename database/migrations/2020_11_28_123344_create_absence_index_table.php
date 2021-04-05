<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbsenceIndexTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('absence_index', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->date('date');
            $table->foreignId('absence_type_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('location_id')->constrained();

            $table->foreignId('absence_id')->constrained()->cascadeOnDelete();
            $table->decimal('hours');

            $table->unique([
                'absence_type_id',
                'absence_id',
                'user_id',
                'location_id',
                'date',
                'hours'
            ],'absence_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('absence_index');
    }
}
