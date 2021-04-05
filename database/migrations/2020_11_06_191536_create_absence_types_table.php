<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbsenceTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('absence_types', function (Blueprint $table) {
            $table->foreignId('location_id')->constrained();

            $table->id();
            $table->timestamps();
            $table->string('title');
            $table->boolean('affect_vacation_times')->default(false);
            $table->boolean('affect_evaluations')->default(false);

             // Refer to CreateNewUser.php for an explaination of these values
            $table->enum(
                'evaluation_calculation_setting',
                ['target_to_zero','absent_to_target','fixed_value','custom_value']
            )->nullable();

            // Feiertage berücksichtigen (An Feiertagen werden
            // keine Urlaubstage verrechnet und in den Auswertungen
            // zählen die Feiertagsstunden, nicht die Abwesenheitsstunden)
            $table->boolean('regard_holidays')->default(true);

            // Abwesenheitstyp automatisch neuen Mitarbeitern zuweisen
            $table->boolean('assign_new_users')->default(true);

            // Beim Bestätigen einer Abwesenheit, alle Zeiterfassungen im Zeitraum entfernen
            $table->boolean('remove_working_sessions_on_confirm')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('absence_types');
    }
}
