<?php

use Illuminate\Database\Seeder;
use App\Models\TenderProposalApproval;

class TenderProposalApprovalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TenderProposalApproval::truncate();
        DB::statement('ALTER SEQUENCE tender_proposal_approvals_id_seq RESTART WITH 1');

        $data = [
            [
                'purch_org_code' => '1100',
                'approver_1' => 'proc_manager_onshore',
                'approver_2' => 'proc_vp_onshore'
            ],
            [
                'purch_org_code' => '1200',
                'approver_1' => 'proc_manager_offshore',
                'approver_2' => null
            ],
        ];
        TenderProposalApproval::insert($data);

    }
}
