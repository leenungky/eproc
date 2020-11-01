<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('tender_items', function (Blueprint $table) {
            $table->id();
            $table->string('tender_number',32);
            $table->string('number', 32)->nullable(false);
            $table->string('line_number', 32)->nullable(false);
            $table->string('product_code', 32)->nullable(true);
            $table->string('product_group_code', 32)->nullable(true);
            $table->string('description', 256)->nullable(true);
            $table->string('purch_group_code', 32)->nullable(true);
            $table->string('purch_group_name', 32)->nullable(true);
            $table->decimal('qty',19,3)->nullable(true);
            $table->string('uom',32)->nullable(true);
            $table->decimal('est_unit_price',19,2)->nullable(true);
            $table->integer('price_unit')->default(1)->nullable(true);
            $table->string('currency_code',8)->nullable(true);
            $table->decimal('subtotal',19,2)->nullable(true)->comment('qty * est_unit_price');
            $table->string('state',32)->nullable(true);
            $table->timestamp('expected_delivery_date', 0)->nullable(true);
            $table->timestamp('transfer_date', 0)->nullable(true);
            $table->string('account_assignment',32)->nullable(true);
            $table->string('item_category',1)->nullable(true);
            $table->string('gl_account',32)->nullable(true);
            $table->string('cost_code',32)->nullable(true);
            $table->string('requisitioner', 32)->nullable(true);
            $table->string('requisitioner_desc',80)->nullable(true);
            $table->string('tracking_number',80)->nullable(true);
            $table->date('request_date')->nullable(true);
            $table->string('certification')->nullable(true);
            $table->string('material_status')->nullable(true);
            $table->string('plant',32)->nullable(true);
            $table->string('plant_name',64)->nullable(true);
            $table->string('storage_loc',32)->nullable(true);
            $table->string('storage_loc_name',64)->nullable(true);
            $table->decimal('qty_ordered',19,3)->nullable(true);
            $table->string('cost_desc')->nullable(true);
            $table->string('overall_limit')->nullable(true);
            $table->string('expected_limit')->nullable(true);

            $table->string('deleteflg',4)->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->string('deleted_by')->nullable(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(array('tender_number'));
            $table->index(array('number'));
            // $table->index(array('pr_number','pr_line_number'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tender_items');
        DB::statement('DROP SEQUENCE IF EXISTS tender_items_id_seq');
    }
}
