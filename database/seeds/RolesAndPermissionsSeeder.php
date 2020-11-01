<?php
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        try {

            DB::beginTransaction();
            DB::statement('TRUNCATE TABLE roles CASCADE;');
            DB::statement('ALTER SEQUENCE roles_id_seq RESTART WITH 1;');
            
            $this->handle();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            throw $e;
        }

    }

    public function handle(){
        //prerequisite: Permissions Seeder has been called before.
        $role = Role::create(['name' => 'vendor']);

        $buyerPermissions = [
            'applicant_read',
            'candidate_read',
            'vendor_read',
            'vendor_evaluation_read',
            'vendor_evaluation_modify',
        ];
        $role = Role::create(['name' => 'Buyer']);
        $role->syncPermissions($buyerPermissions);


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
        $role = Role::create(['name' => 'QMR']);
        $role->syncPermissions($qmrPermissions);


        $adminVendorPermissions = array_merge($qmrPermissions,[
            'applicant_approval',
            'candidate_approval_inject_sap_number',
        ]);
        $role = Role::create(['name' => 'Admin Vendor']);
        $role->syncPermissions($adminVendorPermissions);

        $procurementManagerPermissions = [
            'applicant_read',
            'candidate_read',
            'vendor_read',
            'vendor_sanction_read',
            'vendor_sanction_approval',
            'vendor_evaluation_read',
            'vendor_evaluation_approval',
        ];
        $role = Role::create(['name' => 'Procurement Manager']);
        $role->syncPermissions($procurementManagerPermissions);

        $role = Role::create(['name' => 'Super Admin']);
   }
}