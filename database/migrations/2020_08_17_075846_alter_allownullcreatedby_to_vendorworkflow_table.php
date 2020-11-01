<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAllownullcreatedbyToVendorworkflowTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE "vendor_workflows" ALTER COLUMN "created_by" DROP NOT NULL');
        DB::statement('ALTER TABLE "vendor_workflows" ALTER COLUMN "created_by" DROP DEFAULT');
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
