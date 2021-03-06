<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnEprocPoNumberToPoAdditionalCosts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // $this->down();

        Schema::table('po_additional_costs', function (Blueprint $table) {
            $table->string('eproc_po_number')->nullable(true);
            $table->bigInteger('vendor_id')->nullable(true);
            $table->string('vendor_code', 16)->nullable(true);
            $table->bigInteger('item_id')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('po_additional_costs', function (Blueprint $table) {
            $table->dropColumn('eproc_po_number');
            $table->dropColumn('vendor_id');
            $table->dropColumn('vendor_code');
            $table->dropColumn('item_id');
        });
    }
}
