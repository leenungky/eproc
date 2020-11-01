<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUniqueTableTenderVendorItemText extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tender_vendor_item_text', function (Blueprint $table) {
            $table->dropIndex(['tender_number','item_id','vendor_id']);
            $table->index(['tender_number','item_id','vendor_id', 'submission_method']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tender_vendor_item_text', function (Blueprint $table) {
            $table->dropIndex(['tender_number','item_id','vendor_id', 'submission_method']);
            $table->index(['tender_number','item_id','vendor_id']);
        });
    }
}
