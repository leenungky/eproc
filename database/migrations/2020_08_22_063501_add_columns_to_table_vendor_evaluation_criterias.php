<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToTableVendorEvaluationCriterias extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendor_evaluation_criterias', function (Blueprint $table) {
            $table->integer('weighting')->nullable(false)->default(0);
            $table->integer('minimum_score')->nullable(false)->default(0);
            $table->integer('maximum_score')->nullable(false)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendor_evaluation_criterias', function (Blueprint $table) {
            $table->dropColumn('weighting');
            $table->dropColumn('minimum_score');
            $table->dropColumn('maximum_score');
        });
    }
}
