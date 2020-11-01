<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRefCompanyCode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('ref_company_code', function (Blueprint $table) {
            $table->id();
            $table->string("company_code");
            $table->string("description");
            $table->string("create_by");
            $table->datetime("deleted_at")->nullable(true);
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
        Schema::dropIfExists('ref_company_code');
        Schema::dropIfExists('ref_purchase_company_group');
    }
}
