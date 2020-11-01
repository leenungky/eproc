<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfileDataToDocumentVendorBisnisPermitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendor_profile_business_permits_status', function (Blueprint $table) {
            $table->string('company_profile_data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendor_profile_business_permits_status', function (Blueprint $table) {
            $table->dropColumn('company_profile_data');
        });
    }
}
