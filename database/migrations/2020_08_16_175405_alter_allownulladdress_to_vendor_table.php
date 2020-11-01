<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAllownulladdressToVendorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE "vendors" ALTER COLUMN "address_1" DROP NOT NULL');
        DB::statement('ALTER TABLE "vendors" ALTER COLUMN "address_2" DROP NOT NULL');
        DB::statement('ALTER TABLE "vendors" ALTER COLUMN "address_3" DROP NOT NULL');
        DB::statement('ALTER TABLE "vendors" ALTER COLUMN "address_4" DROP NOT NULL');
        DB::statement('ALTER TABLE "vendors" ALTER COLUMN "address_5" DROP NOT NULL');
        DB::statement('ALTER TABLE "vendors" ALTER COLUMN "province" DROP NOT NULL');
        DB::statement('ALTER TABLE "vendors" ALTER COLUMN "city" DROP NOT NULL');
        DB::statement('ALTER TABLE "vendors" ALTER COLUMN "sub_district" DROP NOT NULL');
        DB::statement('ALTER TABLE "vendors" ALTER COLUMN "house_number" DROP NOT NULL');
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
