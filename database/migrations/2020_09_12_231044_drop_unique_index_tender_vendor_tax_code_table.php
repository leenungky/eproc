<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DropUniqueIndexTenderVendorTaxCodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE tender_vendor_tax_codes DROP CONSTRAINT tender_vendor_tax_codes_tender_number_vendor_id_item_id_tax_code_index');
        Schema::table('tender_vendor_tax_codes', function (Blueprint $table) {
            $table->index(array('tender_number','vendor_id','item_id','tax_code'));
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
            $table->dropIndex(array('tender_number','vendor_id','item_id','tax_code'));
            $table->unique(array('tender_number','vendor_id','item_id','tax_code'));
        });
    }
}
