<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubmissionMethodTableTenderVendorSubmissionDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tender_vendor_submission_detail', function (Blueprint $table) {
            $table->smallInteger('submission_method')->nullable(true);
            $table->dropIndex(array('tender_number','vendor_id', 'vendor_code', 'bidding_document_id'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tender_vendor_submission_detail', function (Blueprint $table) {
            $table->dropColumn('submission_method');
            $table->index(array('tender_number','vendor_id','vendor_code','bidding_document_id'));
        });
    }
}
