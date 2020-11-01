<?php

use App\Buyer;
use App\Models\Role;
use App\Models\TenderConfigApprovers;
use App\User;
use App\UserExtensions;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TenderApprovers extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $resultRole = DB::select( DB::raw("select last_value from roles_id_seq;") );
        // $resultUser = DB::select( DB::raw("select last_value from users_id_seq;") );

        // $seqIdRole = $resultRole[0]->last_value;
        // $seqIdRole = $seqIdRole +1;
        // $seqIdUser = $resultUser[0]->last_value;

        // $roles = [
        //     [
        //         'id' => ++$seqIdRole,
        //         'name' => 'Proc VP OnShore',
        //         'guard_name' => 'web',
        //     ],
        //     [
        //         'id' => ++$seqIdRole,
        //         'name' => 'Proc Manager OnShore',
        //         'guard_name' => 'web',
        //     ],
        //     [
        //         'id' => ++$seqIdRole,
        //         'name' => 'Proc VP OffShore',
        //         'guard_name' => 'web',
        //     ],
        //     [
        //         'id' => ++$seqIdRole,
        //         'name' => 'Proc Manager OffShore',
        //         'guard_name' => 'web',
        //     ],
        // ];
        // $roleStatement = 'ALTER SEQUENCE roles_id_seq RESTART WITH ' . ($seqIdRole+1) . ';';

        // $users = [
        //     [
        //         'id' => ++$seqIdUser,
        //         'name' => 'Name VP 1',
        //         'userid' => 'proc_vp_onshore',
        //         'email' => config('eproc.default_email'),
        //         'password' => '$2y$10$EdjwiJ3AEtecAOKS23aqD.NdmlTGten8x/OoRnI1oVGJlJXKSe6ZW'
        //     ],
        //     [
        //         'id' => ++$seqIdUser,
        //         'name' => 'Name Manager 1',
        //         'userid' => 'proc_manager_onshore',
        //         'email' => config('eproc.default_email'),
        //         'password' => '$2y$10$EdjwiJ3AEtecAOKS23aqD.NdmlTGten8x/OoRnI1oVGJlJXKSe6ZW'
        //     ],
        //     [
        //         'id' => ++$seqIdUser,
        //         'name' => 'Name VP 2',
        //         'userid' => 'proc_vp_offshore',
        //         'email' => config('eproc.default_email'),
        //         'password' => '$2y$10$EdjwiJ3AEtecAOKS23aqD.NdmlTGten8x/OoRnI1oVGJlJXKSe6ZW'
        //     ],
        //     [
        //         'id' => ++$seqIdUser,
        //         'name' => 'Name Manager 2',
        //         'userid' => 'proc_manager_offshore',
        //         'email' => config('eproc.default_email'),
        //         'password' => '$2y$10$EdjwiJ3AEtecAOKS23aqD.NdmlTGten8x/OoRnI1oVGJlJXKSe6ZW'
        //     ],
        // ];
        // $userStatement = 'ALTER SEQUENCE users_id_seq RESTART WITH ' . ($seqIdUser+1) . ';';

        // $usersEx = [
        //     [
        //         'user_id' => $users[0]['id'],
        //         'position' => 'Procurement VP OnShore'
        //     ],
        //     [
        //         'user_id' => $users[1]['id'],
        //         'position' => 'Procurement Manager OnShore'
        //     ],
        //     [
        //         'user_id' => $users[2]['id'],
        //         'position' => 'Procurement VP OfShore'
        //     ],
        //     [
        //         'user_id' => $users[3]['id'],
        //         'position' => 'Procurement Manager OffShore'
        //     ],
        // ];

        // $modelHasRoles = [];
        // foreach($users as $k => $val){
        //     $modelHasRoles[] = [
        //         'model_id' => $val['id'],
        //         'model_type' => 'App\User',
        //         'role_id' => $roles[$k]['id'],
        //     ];
        // }
        // $buyers = [
        //     [
        //         'user_id' => $users[0]['id'],
        //         'buyer_name' => 'Name VP 1',
        //         'valid_from_date' => '2020-01-01',
        //         'valid_thru_date' => '2020-12-31',
        //         'purch_org_id' => 1,
        //         'purch_group_id' => 2,
        //     ],
        //     [
        //         'user_id' => $users[1]['id'],
        //         'buyer_name' => 'Name Manager 1',
        //         'valid_from_date' => '2020-01-01',
        //         'valid_thru_date' => '2020-12-31',
        //         'purch_org_id' => 1,
        //         'purch_group_id' => 2,
        //     ],
        //     [
        //         'user_id' => $users[2]['id'],
        //         'buyer_name' => 'Name VP 2',
        //         'valid_from_date' => '2020-01-01',
        //         'valid_thru_date' => '2020-12-31',
        //         'purch_org_id' => 2,
        //         'purch_group_id' => 3,
        //     ],
        //     [
        //         'user_id' => $users[3]['id'],
        //         'buyer_name' => 'Name Manager 2',
        //         'valid_from_date' => '2020-01-01',
        //         'valid_thru_date' => '2020-12-31',
        //         'purch_org_id' => 2,
        //         'purch_group_id' => 3,
        //     ],
        // ];

        $configs = [
            [
                'purch_org_id' => 1, // onshore
                'role_id' => Role::where('name','Proc Manager OnShore')->first()->id,
                // 'role_id' => $roles[1]['id'], // proc manager role
                'order' => 1
            ],
            [
                'purch_org_id' => 1, // onshore
                'role_id' => Role::where('name','Proc VP OnShore')->first()->id,
                // 'role_id' => $roles[0]['id'], // proc vp role
                'order' => 2
            ],
            [
                'purch_org_id' => 2, // offshore
                'role_id' => Role::where('name','Proc Manager OffShore')->first()->id,
                // 'role_id' => $roles[3]['id'], // proc manager role
                'order' => 1
            ],
            [
                'purch_org_id' => 2, // offshore
                'role_id' => Role::where('name','Proc VP OffShore')->first()->id,
                // 'role_id' => $roles[2]['id'], // proc vp role
                'order' => 2
            ],
        ];

        try {
            DB::beginTransaction();

            // Role::insert($roles);
            // DB::statement($roleStatement);

            // User::insert($users);
            // DB::statement($userStatement);

            // UserExtensions::insert($usersEx);
            // DB::table('model_has_roles')->insert($modelHasRoles);
            // Buyer::insert($buyers);
            DB::statement('TRUNCATE TABLE tender_config_approvers;');
            TenderConfigApprovers::insert($configs);
            DB::statement('TRUNCATE TABLE tender_signatures;');
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            throw $e;
        }

    }
}
