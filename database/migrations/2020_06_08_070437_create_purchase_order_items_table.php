<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->string('number', 16)->nullable(false);
            $table->string('line_number', 8)->nullable(false);
            $table->string('product_code', 32)->nullable(true);
            $table->bigInteger('purch_group_id')->nullable(true);
            $table->string('project_code', 32)->nullable(true);
            $table->decimal('qty',19,3)->nullable(true);
            $table->string('uom',16)->nullable(true);
            $table->decimal('est_unit_price',19,2)->nullable(true);
            $table->integer('price_unit')->default(1)->nullable(true);
            $table->string('currency_code',8)->nullable(true);
            $table->decimal('subtotal',19,2)->nullable(true)->comment('qty * est_unit_price');
            $table->timestamps();

            $table->unique(['number','line_number']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_order_items');
        DB::statement('DROP SEQUENCE IF EXISTS purchase_order_items_id_seq');
    }
}
