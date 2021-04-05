<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTargetHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('target_hours', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->date('start_date');
            $table->string('hours_per')->default('week');
            $table->decimal('target_hours')->default(40);
            $table->boolean('target_limited')->default(false);

            //target hours based on weeks
            $table->decimal('mon')->default(8);
            $table->decimal('tue')->default(8);
            $table->decimal('wed')->default(8);
            $table->decimal('thu')->default(8);
            $table->decimal('fri')->default(8);
            $table->decimal('sat')->default(0);
            $table->decimal('sun')->default(0);

            //target hours based on month
            $table->boolean('is_mon')->default(true);
            $table->boolean('is_tue')->default(true);
            $table->boolean('is_wed')->default(true);
            $table->boolean('is_thu')->default(true);
            $table->boolean('is_fri')->default(true);
            $table->boolean('is_sat')->default(false);
            $table->boolean('is_sun')->default(false);

            $table->unique(['user_id','start_date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('target_hours');
    }
}
