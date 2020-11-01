<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AlterColumnPrToTenderItemsAndPoItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE tender_items ALTER "cost_code" type VARCHAR(100)');
        DB::statement('ALTER TABLE tender_items ALTER "purch_group_name" type VARCHAR(255)');
        DB::statement('ALTER TABLE tender_items ALTER "requisitioner_desc" type VARCHAR(255)');
        DB::statement('ALTER TABLE tender_items ALTER "plant_name" type VARCHAR(255)');
        DB::statement('ALTER TABLE tender_items ALTER "storage_loc_name" type VARCHAR(255)');

        DB::statement('ALTER TABLE po_items ALTER "cost_code" type VARCHAR(100)');
        DB::statement('ALTER TABLE po_items ALTER "purch_group_name" type VARCHAR(255)');
        DB::statement('ALTER TABLE po_items ALTER "requisitioner_desc" type VARCHAR(255)');
        DB::statement('ALTER TABLE po_items ALTER "plant_name" type VARCHAR(255)');
        DB::statement('ALTER TABLE po_items ALTER "storage_loc_name" type VARCHAR(255)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE tender_items ALTER "cost_code" type VARCHAR(32)');
        DB::statement('ALTER TABLE tender_items ALTER "purch_group_name" type VARCHAR(32)');
        DB::statement('ALTER TABLE tender_items ALTER "requisitioner_desc" type VARCHAR(80)');
        DB::statement('ALTER TABLE tender_items ALTER "plant_name" type VARCHAR(64)');
        DB::statement('ALTER TABLE tender_items ALTER "storage_loc_name" type VARCHAR(64)');

        DB::statement('ALTER TABLE po_items ALTER "cost_code" type VARCHAR(32)');
        DB::statement('ALTER TABLE po_items ALTER "purch_group_name" type VARCHAR(32)');
        DB::statement('ALTER TABLE po_items ALTER "requisitioner_desc" type VARCHAR(80)');
        DB::statement('ALTER TABLE po_items ALTER "plant_name" type VARCHAR(64)');
        DB::statement('ALTER TABLE po_items ALTER "storage_loc_name" type VARCHAR(64)');
    }
}
