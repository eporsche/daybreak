<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVacationEntitlementsTransferTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vacation_entitlements_transfer', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('transferred_to_id')->constrained('vacation_entitlements')->cascadeOnDelete();
            $table->foreignId('transferred_from_id')->constrained('vacation_entitlements')->cascadeOnDelete();
            $table->decimal('days',4,1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vacation_entitlements_transfer');
    }
}
