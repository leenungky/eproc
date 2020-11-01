<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexToVendorEvaluationFormDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendor_evaluation_form_details', function (Blueprint $table) {
            $table->index('vendor_evaluation_id');
            $table->index('criteria_id');
            $table->index('vendor_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendor_evaluation_form_details', function (Blueprint $table) {
            $table->dropIndex('vendor_evaluation_id');
            $table->dropIndex('criteria_id');
            $table->dropIndex('vendor_id');
        });
    }
}
