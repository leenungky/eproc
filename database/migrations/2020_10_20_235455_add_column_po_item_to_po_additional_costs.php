<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnPoItemToPoAdditionalCosts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('po_additional_costs', function (Blueprint $table) {
            $table->string("po_item", 10)->nullable(true);
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
            $table->dropColumn("po_item");
        });
    }
}
