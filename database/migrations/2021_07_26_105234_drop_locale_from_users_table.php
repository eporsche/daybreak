<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropLocaleFromUsersTable extends Migration
{
    /**
     * Run the migrations.
     * Dropping columns in laravel needs to be in a seperate transaction
     * for sqlite to work: https://github.com/laravel/framework/issues/2979
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('locale');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('locale', ['de','en'])->default('de');
        });
    }
}
