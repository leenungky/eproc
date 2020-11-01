<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdatePriceUnitToTenderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE tender_items ALTER COLUMN "price_unit" TYPE numeric(19,3) USING "price_unit"::numeric(19,3)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE tender_items ALTER COLUMN "price_unit" TYPE numeric(19,3) USING "price_unit"::integer');
    }
}
