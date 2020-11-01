<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenderVendorItemTextAwarding extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tender_vendor_item_text_awarding', function (Blueprint $table) {
            $table->id();
            $table->string('tender_number', 32)->nullable(true);
            $table->bigInteger('vendor_id')->nullable(true);
            $table->string('vendor_code', 16)->nullable(true);
            $table->bigInteger('item_id')->nullable(true);
            $table->string('PREQ_NO')->nullable(true); // relate with BANFN
            $table->string('PREQ_ITEM')->nullable(true); // relate with BNFPO
            $table->string('TEXT_ID')->nullable(true);
            $table->string('TEXT_ID_DESC')->nullable(true);
            $table->string('TEXT_FORM')->nullable(true);
            $table->string('TEXT_LINE')->nullable(true);
            $table->string('status', 30)->default('draft');
            $table->integer('line_id')->nullable(true);
            $table->smallInteger('submission_method')->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tender_number','item_id','vendor_id', 'submission_method']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tender_vendor_item_text_awarding');
    }
}