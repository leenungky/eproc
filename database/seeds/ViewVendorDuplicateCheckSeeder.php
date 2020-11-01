<?php

use Illuminate\Database\Seeder;

class ViewVendorDuplicateCheckSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('DROP VIEW IF EXISTS view_vendor_duplicate_check');
        DB::statement('ALTER TABLE "vendor_profile_generals"
            ALTER COLUMN "province" DROP NOT NULL,
            ALTER COLUMN "address_4" DROP NOT NULL,
            ALTER COLUMN "city" DROP NOT NULL;
        ');
        DB::statement('
            CREATE OR REPLACE VIEW view_vendor_duplicate_check AS
            select 
            "vendors"."id", 
            "vendors"."tin_number", 
            "vendors"."idcard_number", 
            "vendors"."city", 
            "rc1".city_description AS "city_name",
            "vendors"."vendor_group", 
            "vendors"."registration_status",
            "vendor_profile_taxes"."id" as tax_id,
            "vendor_profile_taxes"."tax_document_type",
            "vendor_profile_taxes"."tax_document_number", 
            "vendor_profile_generals"."id" as general_id,
            "vendor_profile_generals"."city" AS city_general,
            "rc2".city_description AS "city_general_name"
            from "vendors" 
            inner join "ref_company_types" 
                on "ref_company_types"."id" = "vendors"."company_type_id" 
                and "ref_company_types"."deleted_at" is null 
            left join "vendor_history_statuses" 
                on "vendor_history_statuses"."vendor_id" = "vendors"."id" 
                and "vendor_history_statuses"."status" <> \'rejected\' 
                and "vendor_history_statuses"."deleted_at" is null 
            left join "vendor_profiles" 
                on "vendor_profiles"."vendor_id" = "vendors"."id" 
                and "vendor_profiles"."deleted_at" is null 
            left join "vendor_profile_generals" 
                on "vendor_profile_generals"."vendor_profile_id" = "vendor_profiles"."id" 
                and "vendor_profile_generals"."is_current_data" = true 
                and "vendor_profile_generals"."deleted_at" is null 
            left join "vendor_profile_taxes" 
                on "vendor_profile_taxes"."vendor_profile_id" = "vendor_profiles"."id" 
                and "vendor_profile_taxes"."is_current_data" = true 
                and "vendor_profile_taxes"."deleted_at" is null 
                and "vendor_profile_taxes"."tax_document_type" in (\'ID1\',\'ID4\',\'ZZ1\')
            left JOIN "ref_cities" AS "rc1"
                ON "vendors"."city" = "rc1"."city_code"
            left JOIN "ref_cities" AS "rc2"
                ON "vendor_profile_generals"."city" = "rc2"."city_code"
            order by vendors.registration_status asc, vendors.id asc
        ');
    }
}
