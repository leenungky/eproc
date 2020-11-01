<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFulladdressToVendorprofileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->string('street', 255)->nullable(true);
            $table->string('building_name', 255)->nullable(true);
            $table->string('kavling_floor_number', 255)->nullable(true);
            $table->string('village', 255)->nullable(true);
            $table->string('rt', 100)->nullable(true);
            $table->string('rw', 100)->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn('street');
            $table->dropColumn('building_name');
            $table->dropColumn('kavling_floor_number');
            $table->dropColumn('village');
            $table->dropColumn('rt');
            $table->dropColumn('rw');
        });
    }
}
