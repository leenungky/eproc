<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCriteriaGroupToVendorEvaluationGenerals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendor_evaluation_generals', function (Blueprint $table) {
            $table->integer('criteria_group_id')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendor_evaluation_generals', function (Blueprint $table) {
            $table->dropColumn('criteria_group_id');
        });
    }
}
