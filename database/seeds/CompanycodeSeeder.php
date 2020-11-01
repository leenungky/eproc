<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanycodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ref_company_code')->insert([
            'company_code' => "1000",
            'description' => "Onshore",
            'create_by' => "initial",
        ]);
        
        DB::table('ref_assign_purchorg_compcode')->insert([
            'purchase_org_code' => "1100",
            'company_code' => "1000",
        ]);
    }
}
