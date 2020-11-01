<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\DB;

class RefCountrySeeder extends Seeder{

    public function run(){
        Eloquent::unguard();

        $path = base_path().'/database/sql/eproc_countries_provinces_cities.sql';
        DB::unprepared(file_get_contents($path));

        DB::unprepared("
        INSERT INTO ref_provinces (
            SELECT country_code, 0 as region_code, country_description AS region_description 
                FROM ref_countries 
                WHERE country_code NOT IN (SELECT DISTINCT(country_code) FROM ref_provinces)
            );
        ");

        DB::unprepared("
        INSERT INTO ref_cities (
            SELECT country_code, 0 as region_code, concat(country_code, 0, 0) as city_code, country_description AS city_description 
                FROM ref_countries 
                WHERE country_code NOT IN (SELECT DISTINCT(country_code) FROM ref_cities)
            );
        ");

        DB::unprepared("
        INSERT INTO ref_cities (
            SELECT country_code, region_code, concat(country_code, region_code, 0) AS city_code, region_description AS city_description
                FROM (
                    SELECT p.country_code, p.region_code, p.region_description, c.city_code, c.city_description from ref_provinces p
                        LEFT JOIN ref_cities c
                        ON c.country_code = p.country_code
                        AND c.region_code = p.region_code
                ) a 
                WHERE city_code IS NULL
            )
        ");

        DB::unprepared("
        INSERT INTO ref_sub_districts (
            SELECT country_code, 0 as region_code, concat(country_code, 0, 0) as city_code, concat(country_code, 0, 0, 0) as district_code, country_description AS district_description 
                FROM ref_countries 
                WHERE country_code NOT IN (SELECT DISTINCT(country_code) FROM ref_sub_districts)
            );
        ");

        DB::unprepared("
        INSERT INTO ref_sub_districts (
            SELECT country_code, region_code, city_code, concat(country_code, region_code, city_code, 0) as district_code, city_description AS district_description
                FROM (
                    SELECT p.country_code, p.region_code, p.city_code, p.city_description, c.district_code, c.district_description from ref_cities p
                        LEFT JOIN ref_sub_districts c
                        ON c.country_code = p.country_code
                        AND c.region_code = p.region_code
                        and c.city_code = p.city_code
                ) a 
                WHERE district_code IS NULL
            )
        ");

        $this->command->info('Country Etc table seeded!');
    }
}