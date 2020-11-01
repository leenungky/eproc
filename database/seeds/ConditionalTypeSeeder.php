<?php

use App\Models\ConditionalType;
use Illuminate\Database\Seeder;

use App\RefListOption;
use Illuminate\Support\Facades\DB;

class ConditionalTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('TRUNCATE TABLE conditional_types;');
        DB::statement('ALTER SEQUENCE conditional_types_id_seq RESTART WITH 1;');
        $data = [
            [
                'type' => 'ZSDA',
                'description' => 'Discount %',
                'calculation_type' => ConditionalType::TYPE_PERCENTAGE,
                'calculation_pos' => ConditionalType::POSITION_MINUS,
            ],
            [
                'type' => 'ZSDB',
                'description' => 'Discount Value',
                'calculation_type' => ConditionalType::TYPE_VALUE,
                'calculation_pos' => ConditionalType::POSITION_MINUS,
            ],
            [
                'type' => 'ZFRA',
                'description' => 'Freight %',
                'calculation_type' => ConditionalType::TYPE_PERCENTAGE,
                'calculation_pos' => ConditionalType::POSITION_PLUS,
            ],
            [
                'type' => 'ZFRB',
                'description' => 'Freight (Value)',
                'calculation_type' => ConditionalType::TYPE_VALUE,
                'calculation_pos' => ConditionalType::POSITION_PLUS,
            ],
            [
                'type' => 'ZUCA',
                'description' => 'Unloading Cost %',
                'calculation_type' => ConditionalType::TYPE_PERCENTAGE,
                'calculation_pos' => ConditionalType::POSITION_PLUS,
            ],
            [
                'type' => 'ZUCB',
                'description' => 'Unloading Cost Value',
                'calculation_type' => ConditionalType::TYPE_VALUE,
                'calculation_pos' => ConditionalType::POSITION_PLUS,
            ],

            [
                'type' => 'ZLCA',
                'description' => 'Loading Cost %',
                'calculation_type' => ConditionalType::TYPE_PERCENTAGE,
                'calculation_pos' => ConditionalType::POSITION_PLUS,
            ],
            [
                'type' => 'ZCBA',
                'description' => 'Loading Cost Value',
                'calculation_type' => ConditionalType::TYPE_VALUE,
                'calculation_pos' => ConditionalType::POSITION_PLUS,
            ],
            [
                'type' => 'ZSFA',
                'description' => 'Surveyor Fee %',
                'calculation_type' => ConditionalType::TYPE_PERCENTAGE,
                'calculation_pos' => ConditionalType::POSITION_PLUS,
            ],
            [
                'type' => 'ZSFB',
                'description' => 'Surveyor Fee Value',
                'calculation_type' => ConditionalType::TYPE_VALUE,
                'calculation_pos' => ConditionalType::POSITION_PLUS,
            ],
            [
                'type' => 'ZPBB',
                'description' => 'PBBKB (Value)',
                'calculation_type' => ConditionalType::TYPE_VALUE,
                'calculation_pos' => ConditionalType::POSITION_PLUS,
            ],
            [
                'type' => 'ZIN1',
                'description' => 'Insurance %',
                'calculation_type' => ConditionalType::TYPE_PERCENTAGE,
                'calculation_pos' => ConditionalType::POSITION_PLUS,
            ],
            [
                'type' => 'ZIN2',
                'description' => 'Insurance Value',
                'calculation_type' => ConditionalType::TYPE_VALUE,
                'calculation_pos' => ConditionalType::POSITION_PLUS,
            ],
            [
                'type' => 'ZWL1',
                'description' => 'Warehouse Lease %',
                'calculation_type' => ConditionalType::TYPE_PERCENTAGE,
                'calculation_pos' => ConditionalType::POSITION_PLUS,
            ],
            [
                'type' => 'ZWL2',
                'description' => 'Warehouse Lease Val',
                'calculation_type' => ConditionalType::TYPE_VALUE,
                'calculation_pos' => ConditionalType::POSITION_PLUS,
            ],
            [
                'type' => 'ZBOC',
                'description' => 'Bank Of Charge Value',
                'calculation_type' => ConditionalType::TYPE_VALUE,
                'calculation_pos' => ConditionalType::POSITION_PLUS,
            ],
            [
                'type' => 'ZHFE',
                'description' => 'Handling Fee %',
                'calculation_type' => ConditionalType::TYPE_PERCENTAGE,
                'calculation_pos' => ConditionalType::POSITION_PLUS,
            ],
        ];

        ConditionalType::insert($data);
    }
}
