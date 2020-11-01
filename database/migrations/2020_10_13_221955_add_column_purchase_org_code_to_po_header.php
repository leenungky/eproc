<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnPurchaseOrgCodeToPoHeader extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('po_header', function (Blueprint $table) {
            $table->string("purchase_org_code",8)->nullable(true);
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
            $table->dropColumn("purchase_org_code");
        });
    }
}
