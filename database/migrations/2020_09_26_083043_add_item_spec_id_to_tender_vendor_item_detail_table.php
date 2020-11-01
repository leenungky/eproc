<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddItemSpecIdToTenderVendorItemDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tender_vendor_item_detail', function (Blueprint $table) {
            $table->integer('item_spec_id')->nullable(true);
        });
        DB::statement(
            'UPDATE tender_vendor_item_detail tvid SET item_spec_id = tid.line_id FROM tender_item_detail tid ' .
            'WHERE tid.description=tvid.description and tid.requirement=tvid.requirement and tid.reference=tvid.reference'
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tender_vendor_item_detail', function (Blueprint $table) {
            $table->dropColumn('item_spec_id');
        });
    }
}
