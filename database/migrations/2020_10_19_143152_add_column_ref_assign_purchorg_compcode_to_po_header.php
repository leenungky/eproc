<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnRefAssignPurchorgCompcodeToPoHeader extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('po_header', function (Blueprint $table) {
            $table->integer("assign_purchorg_company_code_id")->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('po_header', function (Blueprint $table) {
            $table->dropColumn("assign_purchorg_company_code_id");
        });
    }
}
