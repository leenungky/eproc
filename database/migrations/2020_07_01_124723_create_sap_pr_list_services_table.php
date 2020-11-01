<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSapPrListServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('sap_pr_list_services', function (Blueprint $table) {
            $table->id();
            $table->string('BANFN')->nullable(true);
            $table->string('BNFPO')->nullable(true);
            $table->string('EXTROW')->nullable(true);
            $table->string('SRVPOS')->nullable(true);
            $table->string('KTEXT1')->nullable(true);
            $table->string('MENGE')->nullable(true);
            $table->string('MEINS')->nullable(true);
            $table->string('WAERS')->nullable(true);
            $table->string('BRTWR')->nullable(true);
            $table->string('NETWR')->nullable(true);
            $table->string('COST_CODE')->nullable(true);
            $table->string('COST_DESC')->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->timestamps();
            $table->softDeletes();

            // $table->unique(['BANFN','BNFPO']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sap_pr_list_services');
        DB::statement('DROP SEQUENCE IF EXISTS sap_pr_list_services_id_seq');
    }
}
