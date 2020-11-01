<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddSubmissionMethodTableTenderHeaderTechnical extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tender_header_technical', function (Blueprint $table) {
            $table->smallInteger('submission_method')->nullable(true);
            $table->dropUnique(array('tender_number', 'vendor_id'));
        });

        DB::table('tender_header_technical')->update([
            'submission_method' => 3
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tender_header_technical', function (Blueprint $table) {
            $table->dropColumn('submission_method');
            $table->unique(array('tender_number', 'vendor_id'));
        });
    }
}