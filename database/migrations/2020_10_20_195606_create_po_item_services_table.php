<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePoItemServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('po_item_detail_services', function (Blueprint $table) {
            $table->id();
            $table->string('tender_number', 32)->nullable(true);
            $table->bigInteger('vendor_id')->nullable(true);
            $table->string('vendor_code', 16)->nullable(true);
            $table->string("eproc_po_number", 15)->nullable(true);
            $table->bigInteger('item_id')->nullable(true);
            $table->string('po_item', 10)->nullable(true);
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
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('po_item_detail_services');
    }
}
