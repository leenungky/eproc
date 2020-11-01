<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterColumnTypeToTenderItemDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tender_item_detail', function (Blueprint $table) {
            DB::statement('ALTER TABLE "tender_item_detail" ALTER COLUMN "description" TYPE TEXT');
            DB::statement('ALTER TABLE "tender_item_detail" ALTER COLUMN "requirement" TYPE TEXT');
            DB::statement('ALTER TABLE "tender_item_detail" ALTER COLUMN "reference" TYPE TEXT');
            DB::statement('ALTER TABLE "tender_vendor_item_detail" ALTER COLUMN "description" TYPE TEXT');
            DB::statement('ALTER TABLE "tender_vendor_item_detail" ALTER COLUMN "requirement" TYPE TEXT');
            DB::statement('ALTER TABLE "tender_vendor_item_detail" ALTER COLUMN "reference" TYPE TEXT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tender_item_detail', function (Blueprint $table) {
            DB::statement('ALTER TABLE "tender_item_detail" ALTER COLUMN "description" TYPE VARCHAR(255)');
            DB::statement('ALTER TABLE "tender_item_detail" ALTER COLUMN "requirement" TYPE VARCHAR(255)');
            DB::statement('ALTER TABLE "tender_item_detail" ALTER COLUMN "reference" TYPE VARCHAR(255)');
            DB::statement('ALTER TABLE "tender_vendor_item_detail" ALTER COLUMN "description" TYPE VARCHAR(255)');
            DB::statement('ALTER TABLE "tender_vendor_item_detail" ALTER COLUMN "requirement" TYPE VARCHAR(255)');
            DB::statement('ALTER TABLE "tender_vendor_item_detail" ALTER COLUMN "reference" TYPE VARCHAR(255)');
        });
    }
}
