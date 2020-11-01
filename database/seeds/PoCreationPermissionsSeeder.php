<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class PoCreationPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        //update all workflow
        DB::statement("update tender_workflows set workflow_status='po-creation', page='po-creation' where page='create-po';");

        //update permissions
        DB::statement("update permissions set name = 'tender_po_creation_create' WHERE NAME='tender_create_po_create';");
        DB::statement("update permissions set name = 'tender_po_creation_read' WHERE NAME='tender_create_po_read';");
        DB::statement("update permissions set name = 'tender_po_creation_update' WHERE NAME='tender_create_po_update';");
        DB::statement("update permissions set name = 'tender_po_creation_delete' WHERE NAME='tender_create_po_delete';");

        //refresh permission
        $this->command->comment('Flushing permissions');
        \Illuminate\Support\Facades\Artisan::call('permission:cache-reset');
    }
}
