<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenamePotenderToPoXxxAwarding extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('po_xxx_awarding', function (Blueprint $table) {
            // Schema::rename("po_tender_items", "po_items");
            // Schema::rename("po_tender_additional_costs", "po_additional_costs");
            // Schema::rename("po_tender_header_commercial", "po_header_commercial_awarding");
            // Schema::rename("po_tender_header_technical", "po_header_technical_awarding");
            // Schema::rename("po_tender_item_commercial", "po_item_commercial_awarding");
            // Schema::rename("po_tender_item_technical", "po_item_technical_awarding");
            // Schema::rename("po_tender_item_text", "po_item_text");
            // Schema::rename("po_tender_tax_codes", "po_tax_codes");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('po_xxx_awarding', function (Blueprint $table) {
            
        });
    }
}
