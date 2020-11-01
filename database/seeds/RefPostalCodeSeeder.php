<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\DB;

class RefPostalCodeSeeder extends Seeder{

    public function run(){
        Eloquent::unguard();

        $path = base_path().'/database/sql/ref_postal_codes.sql';
        DB::unprepared(file_get_contents($path));
    }
}