<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOverallLimitToTenderItemCommercialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tender_item_commercial', function (Blueprint $table) {
            $table->decimal('overall_limit', 19,2)->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tender_item_commercial', function (Blueprint $table) {
            $table->dropColumn('submission_method');
        });
    }
}
