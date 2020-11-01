<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePoTermPaymentText extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::create('po_header_term_payment_text', function (Blueprint $table) {
            $table->id();
            $table->string('tender_number', 32)->nullable(true);
            $table->bigInteger('item_id')->nullable(true);
            $table->string('PREQ_NO')->nullable(true); // relate with BANFN
            $table->string('PREQ_ITEM')->nullable(true); // relate with BNFPO
            $table->string('TEXT_ID')->nullable(true);
            $table->string('TEXT_ID_DESC')->nullable(true);
            $table->string('TEXT_FORM')->nullable(true);
            $table->string('TEXT_LINE')->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->smallInteger('action_status')->default(1);
            $table->string('public_status', 30)->default('draft');
            $table->timestamps();
            $table->softDeletes();

            // $table->unique(array('tender_number','vendor_id'));
            $table->index(['tender_number','item_id']);
        });

        DB::statement('insert into po_header_term_payment_text
                (tender_number,item_id,"PREQ_NO","PREQ_ITEM","TEXT_ID","TEXT_ID_DESC","TEXT_FORM","TEXT_LINE",created_at,created_by,updated_at,updated_by)
            select ti.tender_number,ti.line_id as item_id,
                sap."PREQ_NO",sap."PREQ_ITEM",sap."TEXT_ID",sap."TEXT_ID_DESC",sap."TEXT_FORM",sap."TEXT_LINE",
                ti.created_at,ti.created_by,ti.updated_at,ti.updated_by
            from tender_items ti
            join sap_pr_list_item_text sap on ti."number" = sap."PREQ_NO" and ti.line_number = sap."PREQ_ITEM"
            where ti.deleted_at is null;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('po_header_term_payment_text');
    }
}
