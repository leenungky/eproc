<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterScoreToTenderVendorSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tender_vendor_submissions', function (Blueprint $table) {
            DB::statement('ALTER TABLE "tender_vendor_submissions" ALTER COLUMN "score" TYPE numeric(5,2)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tender_vendor_submissions', function (Blueprint $table) {
            DB::statement('ALTER TABLE "tender_vendor_submissions" ALTER COLUMN "score" TYPE integer');
        });
    }
}
