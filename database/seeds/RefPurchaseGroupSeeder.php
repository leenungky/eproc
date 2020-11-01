<?php

use Illuminate\Database\Seeder;

use App\RefPurchaseGroup;

class RefPurchaseGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        RefPurchaseGroup::truncate();
        DB::statement('ALTER SEQUENCE ref_purchase_groups_id_seq RESTART WITH 1');

        $data = [
            [
                'group_code' => '101',
                'description' => 'Mechanical',
            ], [
                'group_code' => '102',
                'description' => 'Civil & Structure',
            ], [
                'group_code' => '103',
                'description' => 'Elec, Inst & Telco',
            ], [
                'group_code' => '104',
                'description' => 'Consumable/General',
            ], [
                'group_code' => '105',
                'description' => 'Piping',
            ], [
                'group_code' => '106',
                'description' => 'Pipeline/Subsea',
            ], [
                'group_code' => '107',
                'description' => 'Mrne & Vsl Chrtr',
            ], [
                'group_code' => '108',
                'description' => 'Lgstc & Formlt',
            ], [
                'group_code' => '109',
                'description' => 'Crw,Formlt,MCU&Trn',
            ], [
                'group_code' => '110',
                'description' => 'Mrne Formlt&Agcy S',
            ], [
                'group_code' => '111',
                'description' => 'Onsh Prj Serv',
            ], [
                'group_code' => '112',
                'description' => 'Offsh Prj Serv',
            ], [
                'group_code' => '113',
                'description' => 'Corporate Service',
            ]
        ];
        RefPurchaseGroup::insert($data); // Eloquent approach
    }
}
