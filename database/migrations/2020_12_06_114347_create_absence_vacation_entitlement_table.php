<?php

use App\Models\Absence;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAbsenceVacationEntitlementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('absence_vacation_entitlement', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('vacation_entitlement_id')->constrained()->cascadeOnDelete();
            $table->foreignId('absence_id')->constrained()->cascadeOnDelete();
            $table->decimal('used_days',4,1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('absence_vacation_entitlement');
    }
}
