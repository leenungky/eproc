<?php

use App\Models\TenderVendor;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddVendorCodeToTableTenderVendors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tender_vendors', function (Blueprint $table) {
            $table->string('vendor_code', 16)->nullable(true);
        });
        DB::statement('UPDATE tender_vendors SET vendor_code=vendors.vendor_code FROM vendors WHERE tender_vendors.vendor_id=vendors.id;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tender_vendors', function (Blueprint $table) {
            $table->dropColumn('vendor_code');
        });
    }
}
