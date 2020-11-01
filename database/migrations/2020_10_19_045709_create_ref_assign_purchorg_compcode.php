<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRefAssignPurchorgCompcode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('ref_assign_purchorg_compcode', function (Blueprint $table) {
            $table->id();
            $table->string("purchase_org_code");
            $table->string("company_code");
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
        Schema::dropIfExists('ref_assign_purchorg_compcode');
        Schema::dropIfExists("ref_assign_purchaseorg_compcode");
    }
}
