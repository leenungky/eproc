<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenderItemsCommercialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tender_item_commercial', function (Blueprint $table) {
            $table->id();
            $table->string('tender_number', 32)->nullable(true);
            $table->bigInteger('vendor_id')->nullable(true);
            $table->string('vendor_code', 16)->nullable(true);
            $table->bigInteger('item_id')->nullable(true);
            $table->decimal('est_unit_price',19,2)->nullable(true);
            $table->decimal('price_unit',19,2)->nullable(true);
            $table->decimal('subtotal',19,2)->nullable(true);
            $table->string('currency_code',8)->nullable(true);
            $table->string('compliance', 16)->nullable(true);
            $table->string('status', 30)->default('draft');
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->string('deleted_by')->nullable(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(array('tender_number','vendor_id','item_id'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tender_item_commercial');
    }
}
