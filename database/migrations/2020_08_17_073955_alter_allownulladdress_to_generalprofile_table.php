<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAllownulladdressToGeneralprofileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE "vendor_profile_generals" ALTER COLUMN "address_1" DROP NOT NULL');
        DB::statement('ALTER TABLE "vendor_profile_generals" ALTER COLUMN "address_2" DROP NOT NULL');
        DB::statement('ALTER TABLE "vendor_profile_generals" ALTER COLUMN "address_3" DROP NOT NULL');
        DB::statement('ALTER TABLE "vendor_profile_generals" ALTER COLUMN "address_4" DROP NOT NULL');
        DB::statement('ALTER TABLE "vendor_profile_generals" ALTER COLUMN "address_5" DROP NOT NULL');
        DB::statement('ALTER TABLE "vendor_profile_generals" ALTER COLUMN "province" DROP NOT NULL');
        DB::statement('ALTER TABLE "vendor_profile_generals" ALTER COLUMN "city" DROP NOT NULL');
        DB::statement('ALTER TABLE "vendor_profile_generals" ALTER COLUMN "sub_district" DROP NOT NULL');
        DB::statement('ALTER TABLE "vendor_profile_generals" ALTER COLUMN "house_number" DROP NOT NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
