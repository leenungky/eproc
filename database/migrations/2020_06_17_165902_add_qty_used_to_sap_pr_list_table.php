<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddQtyUsedToSapPrListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sap_pr_list', function (Blueprint $table) {
            $table->unique(['BANFN','BNFPO']);
        });

        DB::statement('ALTER TABLE sap_pr_list ALTER COLUMN "MENGE" TYPE numeric(19,3) USING "MENGE"::numeric(19,3)');
        DB::statement('ALTER TABLE sap_pr_list ALTER COLUMN "PREIS" TYPE numeric(19,3) USING "PREIS"::numeric(19,3)');
        DB::statement('ALTER TABLE sap_pr_list ALTER COLUMN "PEINH" TYPE numeric(19,3) USING "PEINH"::numeric(19,3)');
        DB::statement('ALTER TABLE sap_pr_list ALTER COLUMN "PREIS2" TYPE numeric(19,3) USING "PREIS2"::numeric(19,3)');
        DB::statement('ALTER TABLE sap_pr_list ALTER COLUMN "BSMNG" TYPE numeric(19,3) USING "BSMNG"::numeric(19,3)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sap_pr_list', function (Blueprint $table) {
            $table->dropUnique('sap_pr_list_banfn_bnfpo_unique');
        });
        DB::statement('ALTER TABLE sap_pr_list ALTER "MENGE" type VARCHAR(100)');
        DB::statement('ALTER TABLE sap_pr_list ALTER "PREIS" type VARCHAR(100)');
        DB::statement('ALTER TABLE sap_pr_list ALTER "PEINH" type VARCHAR(100)');
        DB::statement('ALTER TABLE sap_pr_list ALTER "PREIS2" type VARCHAR(100)');
        DB::statement('ALTER TABLE sap_pr_list ALTER "BSMNG" type VARCHAR(100)');
    }
}
