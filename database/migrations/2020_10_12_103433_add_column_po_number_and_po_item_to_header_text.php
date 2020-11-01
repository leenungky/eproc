<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnPoNumberAndPoItemToHeaderText extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('po_header_text', function (Blueprint $table) {
            $table->string('po_item', 50)->nullable(true);
            $table->string('po_number', 50)->nullable(true);
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
            $table->dropColumn('po_item');
            $table->dropColumn('po_number');
        });
    }
}
