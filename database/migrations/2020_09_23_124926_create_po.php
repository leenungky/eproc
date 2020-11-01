<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('po_list', function (Blueprint $table) {
            $table->id();
            $table->string('tender_number', 32)->nullable(true);
            $table->string('vendor_code', 16)->nullable(true);
            $table->string('eproc_po_number')->nullable(true); // relate with BANFN
            $table->string('sap_po_number')->nullable(true); // relate with BNFPO
            $table->string('eproc_po_status', 32)->nullable(true);
            $table->string('created_on')->nullable(true);    
            $table->timestamp('deleted_at')->nullable(true);           
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
        Schema::dropIfExists('po');
    }
}
