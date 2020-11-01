<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenamePoTenderHeaderCommercialToPoHeaderCommercial extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('po_tender_header_commercial', function (Blueprint $table) {
            Schema::rename("po_tender_header_commercial", "po_header_commercial_awarding");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('po_header_commercial', function (Blueprint $table) {
            //
        });
    }
}
