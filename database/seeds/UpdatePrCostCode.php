<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdatePrCostCode extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement("update tender_items set cost_code = b.\"COST_CODE\" from tender_items a inner join sap_pr_list b on a.\"number\" = b.\"BANFN\" and a.line_number = b.\"BNFPO\"");
        DB::statement("update po_items set cost_code = b.\"COST_CODE\" from po_items a inner join sap_pr_list b on a.\"number\" = b.\"BANFN\" and a.line_number = b.\"BNFPO\"");
    }
}
