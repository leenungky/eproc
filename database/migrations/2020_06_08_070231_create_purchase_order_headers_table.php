<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrderHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('purchase_order_headers', function (Blueprint $table) {
            $table->id();
            $table->string('number', 16);
            $table->date('date');
            $table->string('currency',8)->nullable(true);
            $table->decimal('total',18,2)->nullable(true);
            $table->bigInteger('vendor_id')->nullable(true);
            $table->timestamps();

            $table->unique('number');
            $table->index('vendor_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_order_headers');
        DB::statement('DROP SEQUENCE IF EXISTS purchase_order_headers_id_seq');
    }
}
