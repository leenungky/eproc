<?php
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PermissionsSeeder extends Seeder
{
    private $tenderPages;
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        try {
            $this->tenderPages = config('workflow.tender.pages');
            $roleHasPermissionOld = DB::table('role_has_permissions')->get()->toArray();

            DB::beginTransaction();
            DB::statement('TRUNCATE TABLE permissions CASCADE;');
            DB::statement('ALTER SEQUENCE permissions_id_seq RESTART WITH 1;');
            DB::statement('ALTER SEQUENCE IF EXISTS permissions_id_seq1 RESTART WITH 1;');

            DB::statement('TRUNCATE TABLE roles CASCADE;');
            DB::statement('ALTER SEQUENCE roles_id_seq RESTART WITH 1;');

           $this->createPermissions();

            // // restore role has permission
            // $rolePermissionRestore = [];
            // foreach($roleHasPermissionOld as $val){
            //     $rolePermissionRestore[] = [
            //         'permission_id' => $val->permission_id,
            //         'role_id' => $val->role_id,
            //     ];
            // }
            // if(count($rolePermissionRestore) > 0){
            //     DB::table('role_has_permissions')->insert($rolePermissionRestore);
            // }

            // default role assign permissions
            $this->roleAssignPermissions();

            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            throw $e;
        }
    }

    private function createPermissions()
    {
        // create permissions
        $permissions = [
            ['name' => 'applicant_read'],
            ['name' => 'applicant_approval'],

            ['name' => 'candidate_read'],
            ['name' => 'candidate_approval'],
            ['name' => 'candidate_approval_inject_sap_number'],

            ['name' => 'vendor_read'],
            ['name' => 'vendor_approval'],

            ['name' => 'vendor_sanction_read'],
            ['name' => 'vendor_sanction_modify'],
            ['name' => 'vendor_sanction_approval'],

            ['name' => 'vendor_evaluation_read'],
            ['name' => 'vendor_evaluation_modify'],
            ['name' => 'vendor_evaluation_approval'],

            ['name' => 'vendor_evaluation_score_categories_read'],
            ['name' => 'vendor_evaluation_score_categories_modify'],

            ['name' => 'vendor_evaluation_criteria_read'],
            ['name' => 'vendor_evaluation_criteria_modify'],

            ['name' => 'vendor_evaluation_criteria_group_read'],
            ['name' => 'vendor_evaluation_criteria_group_modify'],

            ['name' => 'user_management'],
            ['name' => 'role_management'],
            ['name' => 'buyer_management'],

            // tender management
            ['name' => 'tender_pr_selection'],
            ['name' => 'tender_index'],
        ];

        foreach($this->tenderPages as $key => $val){
            $permissions[] = ['name' => 'tender_'.$key.'_create'];
            $permissions[] = ['name' => 'tender_'.$key.'_update'];
            $permissions[] = ['name' => 'tender_'.$key.'_read'];
            $permissions[] = ['name' => 'tender_'.$key.'_delete'];
        }

        foreach($permissions as $permission){
            Permission::create($permission);
        }
    }

    private function roleAssignPermissions()
    {
        $tenderPerm = ['tender_index']; // tender admin
        $tenderPermR = ['tender_index']; // tender viewer
        foreach($this->tenderPages as $key => $val){
            $tenderPerm[] = 'tender_'.$key.'_create';
            $tenderPerm[] = 'tender_'.$key.'_update';
            $tenderPerm[] = 'tender_'.$key.'_read';
            $tenderPerm[] = 'tender_'.$key.'_delete';
            $tenderPermR[] = 'tender_'.$key.'_read';
        }

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
            'tender_process_commercial_evaluation_update'
        ]);

        $buyer = Role::findOrCreate('Buyer');
        $buyer->syncPermissions($tenderPerm);

        // tender approver
        $tenderPermR[] = ['tender_procurement_approval_update'];
        $role = Role::findOrCreate('Proc VP OnShore');
        $role->syncPermissions($tenderPermR);
        $role = Role::findOrCreate('Proc Manager OnShore');
        $role->syncPermissions($tenderPermR);
        $role = Role::findOrCreate('Proc VP OffShore');
        $role->syncPermissions($tenderPermR);
        $role = Role::findOrCreate('Proc Manager OffShore');
        $role->syncPermissions($tenderPermR);
        $role = Role::findOrCreate('Procurement Manager');
        $role->syncPermissions($tenderPermR);

        $role = Role::findOrCreate('vendor');
        $role->syncPermissions($tenderPermV);

        $this->roleAssignPermissionsNext();
    }

    public function roleAssignPermissionsNext(){
        //prerequisite: Permissions Seeder has been called before.
        $role = Role::findOrCreate('vendor');

        $buyerPermissions = [
            'applicant_read',
            'candidate_read',
            'vendor_read',
            'vendor_evaluation_read',
            'vendor_evaluation_modify',
        ];
        $role = Role::findOrCreate('Buyer');
        $role->syncPermissions(array_merge($role->permissions->pluck('name')->toArray(), $buyerPermissions));


        $qmrPermissions = [
            'applicant_read',
            'candidate_read',
            'candidate_approval',
            'vendor_read',
            'vendor_approval',
            'vendor_sanction_read',
            'vendor_sanction_modify',
            'vendor_evaluation_score_categories_read',
            'vendor_evaluation_score_categories_modify',
            'vendor_evaluation_criteria_read',
            'vendor_evaluation_criteria_modify',
            'vendor_evaluation_criteria_group_read',
            'vendor_evaluation_criteria_group_modify',
        ];
        $role = Role::findOrCreate('QMR');
        $role->syncPermissions(array_merge($role->permissions->pluck('name')->toArray(), $qmrPermissions));


        $adminVendorPermissions = array_merge($qmrPermissions,[
            'applicant_approval',
            'candidate_approval_inject_sap_number',
        ]);
        $role = Role::findOrCreate('Admin Vendor');
        $role->syncPermissions(array_merge($role->permissions->pluck('name')->toArray(), $adminVendorPermissions));

        $procurementManagerPermissions = [
            'applicant_read',
            'candidate_read',
            'vendor_read',
            'vendor_sanction_read',
            'vendor_sanction_approval',
            'vendor_evaluation_read',
            'vendor_evaluation_approval',
        ];
        $role = Role::findOrCreate('Procurement Manager');
        $role->syncPermissions(array_merge($role->permissions->pluck('name')->toArray(), $procurementManagerPermissions));

        $role = Role::findOrCreate('Super Admin');
   }
}
