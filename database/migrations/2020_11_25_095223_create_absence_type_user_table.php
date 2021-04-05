<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbsenceTypeUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('absence_type_user', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('absence_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id');

            $table->unique(['user_id','absence_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('absence_type_user');
    }
}
