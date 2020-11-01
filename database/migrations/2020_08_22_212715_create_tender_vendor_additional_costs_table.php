<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenderVendorAdditionalCostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tender_vendor_additional_costs', function (Blueprint $table) {
            $table->id();
            $table->string('tender_number', 32)->nullable(true);
            $table->bigInteger('vendor_id')->nullable(true);
            $table->string('vendor_code', 16)->nullable(true);
            $table->bigInteger('item_id')->nullable(true);
            $table->string('conditional_code', 6)->nullable(true);
            $table->string('conditional_name', 64)->nullable(true);
            $table->integer('percentage')->nullable(true);
            $table->decimal('value', 18,2)->nullable(true);
            $table->smallInteger('calculation_pos')->nullable(true);
            $table->string('conditional_type', 64)->nullable(true);
            $table->smallInteger('submission_method')->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->string('deleted_by')->nullable(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tender_number','item_id','vendor_id']);
            $table->index(['tender_number','vendor_id','conditional_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tender_vendor_additional_costs');
    }
}
