<?php

use Illuminate\Database\Seeder;

class RefCompanyGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('INSERT INTO ref_company_groups ("id", "name", "description", "last_number","created_at","updated_at")
        SELECT \'Z001\' AS "id", \'local\' AS "name", \'Local\' AS "description", COALESCE((MAX(id)+1), 1) AS last_number, now(), now() FROM vendors WHERE vendor_code LIKE \'D%\'
        union
        SELECT \'Z002\' AS "id", \'foreign\' AS "name", \'Foreign\' AS "description", COALESCE((MAX(id)+1), 1) AS last_number, now(), now() FROM vendors WHERE vendor_code LIKE \'L%\'
        ');
    }
}
