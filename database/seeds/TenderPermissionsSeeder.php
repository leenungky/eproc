<?php

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\TenderPermission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TenderPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TenderPermission::truncate();
        DB::statement('ALTER SEQUENCE tender_permissions_id_seq RESTART WITH 1');

        $data = [
            ['name' => 'prequalification_start','page' => 'process_prequalification', 'order' => 1],
            ['name' => 'prequalification_open','page' => 'process_prequalification', 'order' => 2],
            ['name' => 'prequalification_finish','page' => 'process_prequalification', 'order' => 3],
            ['name' => 'single_envelope_start','page' => '', 'order' => 4],
            ['name' => 'dual_envelope_start','page' => '', 'order' => 5],
            ['name' => 'technical_start','page' => '', 'order' => 6],
            ['name' => 'commercial_start','page' => '', 'order' => 7],
            ['name' => 'single_envelope_open','page' => '', 'order' => 8],
            ['name' => 'technical_open','page' => '', 'order' => 9],
            ['name' => 'commercial_open','page' => '', 'order' => 10],
            ['name' => 'single_envelope_finish','page' => '', 'order' => 11],
            ['name' => 'technical_finish','page' => '', 'order' => 12],
            ['name' => 'commercial_finish','page' => '', 'order' => 13],
            ['name' => 'negotiation_start','page' => '', 'order' => 14],
            ['name' => 'negotiation_open','page' => '', 'order' => 15],
            ['name' => 'negotiation_finish','page' => '', 'order' => 16],
        ];

        $tenderPages = config('workflow.tender.pages');
        try {
            DB::beginTransaction();
            TenderPermission::insert($data); // Eloquent approach
            $this->createPermission($tenderPages);
            $this->roleAddPermission($tenderPages);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            throw $e;
        }

    }

    private function createPermission($tenderPages)
    {
        $permissions = [];
        foreach($tenderPages as $key => $val){
            $permissions[] = ['name' => 'tender_'.$key.'_create'];
            $permissions[] = ['name' => 'tender_'.$key.'_update'];
            $permissions[] = ['name' => 'tender_'.$key.'_read'];
            $permissions[] = ['name' => 'tender_'.$key.'_delete'];
        }
        $permissions[] = ['name' => 'tender_process_commercial_approval'];
        
        foreach($permissions as $permission){
            Permission::updateOrCreate($permission);
        }
    }

    private function roleAddPermission($tenderPages)
    {
        $tenderPerm = ['tender_index']; // tender admin
        $tenderPermR = ['tender_index']; // tender viewer
        foreach($tenderPages as $key => $val){
            $tenderPerm[] = 'tender_'.$key.'_create';
            $tenderPerm[] = 'tender_'.$key.'_update';
            $tenderPerm[] = 'tender_'.$key.'_read';
            $tenderPerm[] = 'tender_'.$key.'_delete';
            $tenderPermR[] = 'tender_'.$key.'_read';
        }

        // permission vendor
        // exclude permission
        $tenderPermV = array_diff( $tenderPermR, [
            'tender_process_registration_read',
            'tender_internal_documents_read',
            'tender_proposed_vendors_read',
            'tender_evaluators_read',
            'tender_weightings_read',
            'tender_procurement_approval_read',
        ] );

        // include permission
        $tenderPermV = array_merge( $tenderPermV, [
            'tender_process_prequalification_update',
            'tender_process_tender_evaluation_update',
            'tender_process_technical_evaluation_update',
            'tender_process_commercial_evaluation_update',
            'tender_negotiation_create',
            'tender_negotiation_read',
            'tender_negotiation_update',
            'tender_negotiation_delete',
        ]);

        $role = Role::findOrCreate('vendor');
        $role->syncPermissions($tenderPermV);

        //refresh permission
        $this->command->comment('Flushing permissions');
        \Illuminate\Support\Facades\Artisan::call('permission:cache-reset');
    }
}
