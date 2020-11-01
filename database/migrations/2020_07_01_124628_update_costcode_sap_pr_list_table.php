<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateCostcodeSapPrListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE sap_pr_list RENAME "COSTCODE" TO "COST_CODE"');
        DB::statement('ALTER TABLE sap_pr_list RENAME "COSTDESC" TO "COST_DESC"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE sap_pr_list RENAME "COST_CODE" TO "COSTCODE"');
        DB::statement('ALTER TABLE sap_pr_list RENAME "COST_DESC" TO "COSTDESC"');
    }
}
