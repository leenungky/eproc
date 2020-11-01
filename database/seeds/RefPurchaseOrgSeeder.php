<?php

use Illuminate\Database\Seeder;

use App\RefPurchaseOrg;

class RefPurchaseOrgSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        RefPurchaseOrg::truncate();
        DB::statement('ALTER SEQUENCE ref_purchase_orgs_id_seq RESTART WITH 1');

        $data = [
            [
                'org_code' => '1100',
                'description' => 'Purch. Org Onshore',
            ], [
                'org_code' => '1200',
                'description' => 'Purch. Org Offshore',
            ]
        ];
        RefPurchaseOrg::insert($data); // Eloquent approach
    }
}
