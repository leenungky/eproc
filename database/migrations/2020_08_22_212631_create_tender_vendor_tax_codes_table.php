<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenderVendorTaxCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tender_vendor_tax_codes', function (Blueprint $table) {
            $table->id();
            $table->string('tender_number', 32)->nullable(true);
            $table->bigInteger('vendor_id')->nullable(true);
            $table->string('vendor_code', 16)->nullable(true);
            $table->bigInteger('item_id')->nullable(true);
            $table->string('tax_code', 4)->nullable(true);
            $table->string('description', 64)->nullable(true);
            $table->smallInteger('submission_method')->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->string('deleted_by')->nullable(true);
            $table->softDeletes();
            $table->timestamps();

            $table->unique(array('tender_number','vendor_id','item_id','tax_code'));
            $table->index(['tender_number','item_id','vendor_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tender_vendor_tax_codes');
    }
}
