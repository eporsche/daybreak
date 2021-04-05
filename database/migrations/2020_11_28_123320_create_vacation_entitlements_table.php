<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVacationEntitlementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vacation_entitlements', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->boolean('expires')->default(true);
            $table->decimal('days',4,1)->default(0);
            $table->string('status', 25)->default('expires');
            $table->boolean('transfer_remaining')->default(false);
            $table->date('end_of_transfer_period')->nullable();
            // Sie können den Zeitraum eines Urlaubskontingents nur ändern, so lange die Anzahl verbrauchter Tage bzw. Stunden Null ist.
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vacation_budgets');
    }
}
