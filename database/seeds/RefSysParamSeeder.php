<?php

use Illuminate\Database\Seeder;

use App\Models\Ref\RefSysParam;
use Illuminate\Support\Facades\DB;

class RefSysParamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        RefSysParam::truncate();
        DB::statement('ALTER SEQUENCE ref_sys_params_id_seq RESTART WITH 1');
        $data = [
            //submission method
            [
                'name'=>'last_avl_number',
                'value1'=>'0',
                'value2'=>'',
                'comment' => 'AVL Number',
            ],
        ];
        RefSysParam::insert($data);
    }
}
