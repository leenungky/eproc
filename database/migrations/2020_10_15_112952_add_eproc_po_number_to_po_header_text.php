<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEprocPoNumberToPoHeaderText extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('po_header_text', function (Blueprint $table) {
            $table->string('eproc_po_number', 15)->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('po_header_text', function (Blueprint $table) {
            $table->dropColumn("eproc_po_number");
        });
    }
}
