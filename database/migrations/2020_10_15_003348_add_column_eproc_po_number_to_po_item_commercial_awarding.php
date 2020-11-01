<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnEprocPoNumberToPoItemCommercialAwarding extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // $this->down();
        Schema::table('po_item_commercial_awarding', function (Blueprint $table) {
            $table->string('eproc_po_number')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('po_item_commercial_awarding', function (Blueprint $table) {
            $table->dropColumn('eproc_po_number');
        });
    }
}
