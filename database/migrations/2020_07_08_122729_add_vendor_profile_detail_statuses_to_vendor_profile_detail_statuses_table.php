<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVendorProfileDetailStatusesToVendorProfileDetailStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendor_profile_detail_statuses', function (Blueprint $table) {
            //$table->string('update_vendor_data_status', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendor_profile_detail_statuses', function (Blueprint $table) {
            $table->dropColumn('update_vendor_data_status');
        });
    }
}
