<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyTabelRefAssignPurchaseorgCompcode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::table('ref_assign_purchaseorg_compcode', function (Blueprint $table) {
            $table->integer('purchase_compcode_id')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ref_assign_purchaseorg_compcode', function (Blueprint $table) {
            $table->dropColumn("purchase_org_code");
            $table->dropColumn("description");
            $table->dropColumn("company_code");
        });
    }
}
