<?php

use Illuminate\Database\Seeder;

use App\RefListOption;
use Illuminate\Support\Facades\DB;

class ConditionalTypeOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $result = DB::select( DB::raw("select last_value from ref_list_options_id_seq;") );
        $seqId = $result[0]->last_value;
        $data = [
            [
                'id'                => ++$seqId,
                'type'               => 'conditional_type_option',
                'key'               => 'CT1',
                'value'              => 'l_header',
            ],
            [
                'id'                => ++$seqId,
                'type'               => 'conditional_type_option',
                'key'               => 'CT2',
                'value'              => 'l_item',
            ],
        ];

        RefListOption::insert($data);
        DB::statement('ALTER SEQUENCE ref_list_options_id_seq RESTART WITH ' . ($seqId+1) .';');
    }
}
