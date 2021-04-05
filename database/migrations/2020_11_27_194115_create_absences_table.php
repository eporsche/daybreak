<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbsencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('absences', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('absence_type_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('location_id')->constrained();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('full_day')->default(true);
            $table->boolean('force_calc_custom_hours')->default(false);
            $table->decimal('paid_hours')->nullable(); //required if force_calc_custom_hours is true
            $table->decimal('vacation_days')->nullable(); //required if force_calc_custom_hours is true
            $table->string('status', 25)->default('pending');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('absences');
    }
}
