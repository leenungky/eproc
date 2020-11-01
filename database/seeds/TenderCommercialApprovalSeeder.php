<?php

use Illuminate\Database\Seeder;
use App\Models\TenderCommercialApproval;

class TenderCommercialApprovalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TenderCommercialApproval::truncate();
        DB::statement('ALTER SEQUENCE tender_commercial_approvals_id_seq RESTART WITH 1');

        $data = [
            [
                'purch_org_code' => '1100',
                'item_category' => '0',
                'description' => 'onshore-material',
                'approver_1' => 'proc_manager_onshore',
                'approver_2' => null
            ],
            [
                'purch_org_code' => '1100',
                'item_category' => '9',
                'description' => 'onshore-service',
                'approver_1' => 'proc_manager_onshore',
                'approver_2' => 'proc_vp_onshore'
            ],
            [
                'purch_org_code' => '1200',
                'item_category' => '0',
                'description' => 'offshore-material',
                'approver_1' => 'proc_manager_offshore',
                'approver_2' => null
            ],
            [
                'purch_org_code' => '1200',
                'item_category' => '9',
                'description' => 'offshore-service',
                'approver_1' => 'proc_manager_offshore',
                'approver_2' => 'proc_vp_offshore'
            ],
        ];
        TenderCommercialApproval::insert($data);
    }
}
