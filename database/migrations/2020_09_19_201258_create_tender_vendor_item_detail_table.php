<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenderVendorItemDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tender_vendor_item_detail', function (Blueprint $table) {
            $table->id();
            $table->string('tender_number', 32)->nullable(true);
            $table->bigInteger('vendor_id')->nullable(true);
            $table->string('vendor_code', 16)->nullable(true);
            $table->text('description')->nullable(true);
            $table->string('requirement')->nullable(true);
            $table->string('reference')->nullable(true);
            $table->text('data')->nullable(true);
            $table->text('respond')->nullable(true);
            $table->integer('category_id')->nullable(true);
            $table->smallInteger('submission_method')->nullable(true);
            $table->smallInteger('action_status')->default(1);
            $table->string('status', 30)->default('draft');
            $table->integer('line_id')->nullable(true);
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
        Schema::dropIfExists('tender_vendor_item_detail');
    }
}
