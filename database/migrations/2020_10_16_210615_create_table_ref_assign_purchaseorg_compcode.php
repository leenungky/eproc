<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableRefAssignPurchaseorgCompcode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ref_assign_purchaseorg_compcode', function (Blueprint $table) {
            $table->id();
            $table->string("tender_number", 15)->nullable(true);
            $table->string("company_code")->nullable(true);
            $table->string("purchase_org_code")->nullable(true);
            $table->string("description", 255)->nullable(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ref_assign_purchaseorg_compcode');
    }
}