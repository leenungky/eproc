<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Buyer;
use App\Models\Ref\RefBuyer;
use App\Models\Ref\RefBuyerPurchOrg;
use App\Models\Ref\RefBuyerPurchGroup;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $vendors = User::where('user_type','vendor')->get();
        $defaultEmail=config('eproc.default_email');
        $onshoreEmail=config('eproc.default_onshore_email');
        $offshoreEmail=config('eproc.default_offshore_email');

        DB::statement('TRUNCATE TABLE users CASCADE;');
        DB::statement('ALTER SEQUENCE users_id_seq RESTART WITH 1;');
        DB::statement('TRUNCATE TABLE user_extensions CASCADE;');
        DB::statement('ALTER SEQUENCE user_extensions_id_seq RESTART WITH 1;');
        DB::table('users')->insert([
            [
                'name' => 'admin',
                'userid' => 'admin',
                'email' => $defaultEmail,
                'password' => Hash::make('password'), // password
                'user_type' => 'admin',
            ],
        ]);

        //super admin
        $user = User::find(1)->assignRole('Super Admin');
        //create dummy users with buyer info
        $users = [
            ['name' => 'Admin Offshore', 'userid' => 'admin_offshore', 'role' => 'Admin Vendor', 'purch_org_id' => 2, 'purch_group_id' => 1, 'email'=>$offshoreEmail],
            ['name' => 'Admin Onshore', 'userid' => 'admin_onshore', 'role' => 'Admin Vendor', 'purch_org_id' => 1, 'purch_group_id' => 1, 'email'=>$onshoreEmail],
            ['name' => 'QMR Offshore', 'userid' => 'qmr_offshore', 'role' => 'QMR', 'purch_org_id' => 2, 'purch_group_id' => 1, 'email'=>$offshoreEmail],
            ['name' => 'QMR Onshore', 'userid' => 'qmr_onshore', 'role' => 'QMR', 'purch_org_id' => 1, 'purch_group_id' => 1, 'email'=>$onshoreEmail],
            ['name' => 'Proc Manager Offshore', 'userid' => 'mgr_offshore', 'role' => 'Procurement Manager', 'purch_org_id' => 2, 'purch_group_id' => 1, 'email'=>$offshoreEmail],
            ['name' => 'Proc Manager Onshore', 'userid' => 'mgr_onshore', 'role' => 'Procurement Manager', 'purch_org_id' => 1, 'purch_group_id' => 1, 'email'=>$onshoreEmail],
            ['name' => 'Name VP 1', 'userid' => 'proc_vp_onshore', 'role' => 'Proc VP OnShore', 'purch_org_id' => 1, 'purch_group_id' => 2, 'email'=>$onshoreEmail],
            ['name' => 'Name Manager 1', 'userid' => 'proc_manager_onshore', 'role' => 'Proc Manager OnShore', 'purch_org_id' => 1, 'purch_group_id' => 2, 'email'=>$onshoreEmail],
            ['name' => 'Name VP 2', 'userid' => 'proc_vp_offshore', 'role' => 'Proc VP OffShore', 'purch_org_id' => 2, 'purch_group_id' => 3, 'email'=>$offshoreEmail],
            ['name' => 'Name Manager 2', 'userid' => 'proc_manager_offshore', 'role' => 'Proc Manager OffShore', 'purch_org_id' => 2, 'purch_group_id' => 3, 'email'=>$offshoreEmail],
            ['name' => 'Buyer Offshore', 'userid' => 'buyer_offshore', 'role' => 'Buyer', 'purch_org_id' => 2, 'purch_group_id' => 1, 'email'=>$offshoreEmail],
            ['name' => 'Buyer Onshore', 'userid' => 'buyer_onshore', 'role' => 'Buyer', 'purch_org_id' => 1, 'purch_group_id' => 1, 'email'=>$onshoreEmail],
        ];

        foreach($users as $user){
            $u = new User();
                $u->name = $user['name'];
                $u->userid = $user['userid'];
                $u->email = $user['email'];
                $u->password = Hash::make('password');
            $u->save();

            $u->assignRole($user['role']);
            // Buyer::insert([
            //     'user_id' => $u->id,
            //     'buyer_name' => $user['name'],
            //     'valid_from_date' => date('Y-01-01'),
            //     'valid_thru_date' => date('Y-12-31'),
            //     'purch_org_id' => $user['purch_org_id'],
            //     'purch_group_id' => $user['purch_group_id'],
            // ]);
            RefBuyer::insert([
                'user_id' => $u->id,
                'buyer_name' => $user['name'],
                'valid_from_date' => date('Y-01-01'),
                'valid_thru_date' => date('Y-12-31'),
                'created_by' => 0,
            ]);
            RefBuyerPurchOrg::insert([
                'user_id' => $u->id,
                'purch_org_id' => $user['purch_org_id'],
            ]);
            RefBuyerPurchGroup::insert([
                'user_id' => $u->id,
                'purch_group_id' => $user['purch_group_id'],
            ]);
        }

        foreach($vendors as $user){
            $u = new User();
                $u->name = $user->name;
                $u->userid = $user->userid;
                $u->email = $user->email;
                $u->password = $user->password;
                $u->user_type = 'vendor';
                $u->ref_id = $user->ref_id;
                $u->created_at = $user->created_at;
                $u->updated_at = $user->updated_at;
            $u->save();

            $u->assignRole('vendor');
        }
    }
}
