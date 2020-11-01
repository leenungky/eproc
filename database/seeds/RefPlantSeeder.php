<?php

use Illuminate\Database\Seeder;
use App\RefPlant;

class RefPlantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        RefPlant::truncate();
        DB::statement('ALTER SEQUENCE ref_plants_id_seq RESTART WITH 1');
        $data = [
            [
                'name' => 'Onshore',
            ], [
                'name' => 'Offshore',
            ]
        ];
        RefPlant::insert($data); 
    }
}
