<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterMigrateVendorTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::statement('ALTER TABLE "vendors" ALTER COLUMN "sub_district" DROP NOT NULL');
        DB::statement('ALTER TABLE "vendors" ALTER COLUMN "pic_mobile_number" TYPE VARCHAR(32)');
        DB::statement('ALTER TABLE "vendor_profile_generals" ALTER COLUMN "sub_district" DROP NOT NULL');

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
