<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexToVendorSanctionHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendor_sanction_histories', function (Blueprint $table) {
            $table->index("vendor_sanction_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendor_sanction_histories', function (Blueprint $table) {
            $table->dropIndex("vendor_sanction_id");
        });
    }
}
