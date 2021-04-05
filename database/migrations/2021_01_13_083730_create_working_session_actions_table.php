<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkingSessionActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('working_session_actions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('working_session_id')->constrained()->cascadeOnDelete();
            $table->string('action_type', 255);
            $table->timestamp('action_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('working_session_actions');
    }
}
