<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUpdateVendorTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('vendor_profiles', function (Blueprint $table) {
            $table->string('avl_no')->nullable();
            $table->date('avl_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendor_profiles', function (Blueprint $table) {
            $table->dropColumn(array('avl_no', 'avl_date'));
        });
    }
}
