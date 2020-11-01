<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUniqueTableTenderVendorTaxCodes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tender_vendor_tax_codes', function (Blueprint $table) {
            $table->dropUnique(array('tender_number', 'vendor_id', 'item_id', 'tax_code'));
            $table->dropIndex(['tender_number', 'item_id', 'vendor_id']);

            $table->unique(array('tender_number', 'vendor_id', 'item_id', 'tax_code', 'submission_method'));
            $table->index(['tender_number', 'item_id', 'vendor_id', 'submission_method']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tender_vendor_tax_codes', function (Blueprint $table) {
            $table->dropUnique(array('tender_number', 'vendor_id', 'item_id', 'tax_code', 'submission_method'));
            $table->dropIndex(['tender_number', 'item_id', 'vendor_id', 'submission_method']);

            $table->unique(array('tender_number', 'vendor_id', 'item_id', 'tax_code'));
            $table->index(['tender_number', 'item_id', 'vendor_id']);
        });
    }
}
