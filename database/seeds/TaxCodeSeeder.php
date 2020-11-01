<?php

use App\Models\TaxCode;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaxCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('TRUNCATE TABLE tax_codes;');
        DB::statement('ALTER SEQUENCE tax_codes_id_seq RESTART WITH 1;');
        $data = [
            [
                'tax_code' => 'V0',
                'description' => 'Non PPN Masukan',
            ],
            [
                'tax_code' => 'V1',
                'description' => 'PPN - Masukan 10%',
            ],
            [
                'tax_code' => 'V2',
                'description' => 'PPN - Masukan DPP Nilai Lain – 1%',
            ],
            [
                'tax_code' => 'V3',
                'description' => 'PPN – Masukan Tidak Dipungut',
            ],
            [
                'tax_code' => 'V4',
                'description' => 'PPN – Masukan Dibebaskan',
            ],
            [
                'tax_code' => 'V5',
                'description' => 'PPN – Masukan Import',
            ],
            [
                'tax_code' => 'V6',
                'description' => 'PPN - Kawasan Bebas 10%',
            ],
            [
                'tax_code' => 'V7',
                'description' => 'PPN – Masukan Jasa Luar Negeri',
            ],
        ];

        TaxCode::insert($data);
    }
}
