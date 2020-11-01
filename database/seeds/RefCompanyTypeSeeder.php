<?php

use Illuminate\Database\Seeder;

use App\RefCompanyType;

class RefCompanyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        RefCompanyType::truncate();
        DB::statement('ALTER SEQUENCE ref_company_types_id_seq RESTART WITH 1');

        $data = [
            [
                'company_type' => 'PT',
                'description' => 'Local Vendor',
            ], [
                'company_type' => 'CV',
                'description' => 'Local Vendor',
            ], [
                'company_type' => 'Yayasan',
                'description' => 'Local Vendor',
            ], [
                'company_type' => 'Koperasi',
                'description' => 'Local Vendor',
            ], [
                'company_type' => 'Perum',
                'description' => 'Local Vendor',
            ], [
                'company_type' => 'Toko',
                'description' => 'Local Vendor / Personal Vendor',
            ], [
                'company_type' => 'Company',
                'description' => 'Overseas Vendor',
            ], [
                'company_type' => 'Others',
                'description' => 'Others',
            ], [
                'company_type' => 'Perorangan',
                'description' => 'Mr, Mrs & Personal Vendor',
            ]
        ];
        RefCompanyType::insert($data); // Eloquent approach
    }
}
