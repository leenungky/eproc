<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTabelRefPurchaseCompanyGroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ref_company_code', function (Blueprint $table) {
            $table->id();
            $table->string("company_code")->nullable(false);
            $table->string("purchase_org_code")->nullable(false);
            $table->string("description", 255)->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ref_company_code');
    }
}
