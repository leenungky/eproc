<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableVendorEvaluationGeneralsPurchaseOrgId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendor_evaluation_generals', function (Blueprint $table) {
            $table->dropColumn('purchase_org_id');
        });
        Schema::table('vendor_evaluation_generals', function (Blueprint $table) {
            $table->string('purchase_org_id')->nullable(true);
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
            $table->dropColumn('purchase_org_id');
        });
        Schema::table('vendor_evaluation_generals', function (Blueprint $table) {
            $table->bigInteger('purchase_org_id')->nullable(true);
        });
    }
}