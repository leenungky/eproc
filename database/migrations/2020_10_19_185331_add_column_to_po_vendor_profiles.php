<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToPoVendorProfiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('po_vendor_profile', function (Blueprint $table) {
            $table->string("eproc_po_number", 15)->nullable(true);
            $table->string("tender_number",15)->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('po_vendor_profile', function (Blueprint $table) {
            $table->removeColumn("eproc_po_number");
            $table->removeColumn("tender_number");
        });
    }
}
