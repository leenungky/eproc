<?php

use Illuminate\Database\Seeder;

class UpdateWorkflowPOCreations extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement("update tender_workflows set status = 'active', workflow_status = 'po_creation', page = 'po_creation' where workflow_status = 'create_po'");
    }
}
