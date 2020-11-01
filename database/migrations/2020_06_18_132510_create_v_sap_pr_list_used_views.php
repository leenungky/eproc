<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateVSapPrListUsedViews extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('DROP VIEW IF EXISTS v_sap_pr_list_used');
        $sql = 'CREATE VIEW v_sap_pr_list_used AS
            select "BANFN","BNFPO"
                ,ti.qty as qty_used
                ,(spl."MENGE" - ti.qty) as qty_available
            from sap_pr_list spl
            join (
                select "number",line_number,sum(qty) as qty from tender_items  ti
                join tender_parameters tp on tp.tender_number = ti.tender_number
                where tp.deleted_at is null and ti.deleted_at is null
                group by "number",line_number
            ) ti on spl."BANFN" = ti."number" and spl."BNFPO" = ti.line_number';
        DB::statement($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS v_sap_pr_list_used');
    }
}
