<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenderHeaderTechnicalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tender_header_technical', function (Blueprint $table) {
            $table->id();
            $table->string('tender_number', 32)->nullable(true);
            $table->bigInteger('vendor_id')->nullable(true);
            $table->string('vendor_code', 16)->nullable(true);
            $table->string('quotation_number', 25)->nullable(true);
            $table->dateTime('quotation_date')->nullable(true);
            $table->string('quotation_note', 40)->nullable(true);
            $table->smallInteger('tkdn_percentage')->nullable(true);
            $table->string('quotation_file')->nullable(true);
            $table->string('tkdn_file')->nullable(true);
            $table->string('proposed_item_file')->nullable(true);
            $table->string('status', 30)->default('draft');
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->string('deleted_by')->nullable(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(array('tender_number','vendor_id'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tender_header_technical');
    }
}
