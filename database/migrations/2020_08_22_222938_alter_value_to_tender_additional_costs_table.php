<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterValueToTenderAdditionalCostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE "tender_additional_costs" ALTER COLUMN "value" TYPE NUMERIC(18,2);');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE "tender_additional_costs" ALTER COLUMN "value" TYPE NUMERIC(8,2);');
    }
}
