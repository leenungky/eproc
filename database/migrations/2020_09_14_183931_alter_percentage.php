<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPercentage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE "tender_header_technical" ALTER COLUMN "tkdn_percentage" TYPE NUMERIC(5,2)');
        DB::statement('ALTER TABLE "tender_additional_costs" ALTER COLUMN "percentage" TYPE NUMERIC(5,2)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
