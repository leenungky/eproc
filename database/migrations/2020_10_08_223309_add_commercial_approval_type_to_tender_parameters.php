<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommercialApprovalTypeToTenderParameters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tender_parameters', function (Blueprint $table) {
            $table->string('commercial_approval_type',16)->nullable(true);
            $table->string('commercial_approval_status',16)->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tender_parameters', function (Blueprint $table) {
            $table->dropColumn('commercial_approval_type');
            $table->dropColumn('commercial_approval_status');
        });
    }
}
