<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUniqueTableTenderVendorAdditionalCosts extends Migration
{
     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tender_vendor_additional_costs', function (Blueprint $table) {
            $table->dropIndex(['tender_number','item_id','vendor_id']);
            $table->dropIndex(['tender_number','vendor_id','conditional_type']);

            $table->index(['tender_number','item_id','vendor_id', 'submission_method']);
            $table->index(['tender_number','vendor_id','conditional_type', 'submission_method']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tender_vendor_additional_costs', function (Blueprint $table) {
            $table->dropIndex(['tender_number','item_id','vendor_id', 'submission_method']);
            $table->dropIndex(['tender_number','vendor_id','conditional_type', 'submission_method']);

            $table->index(['tender_number','item_id','vendor_id']);
            $table->index(['tender_number','vendor_id','conditional_type']);
        });
    }
}
