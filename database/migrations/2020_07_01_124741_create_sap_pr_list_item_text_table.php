<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSapPrListItemTextTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('sap_pr_list_item_text', function (Blueprint $table) {
            $table->id();
            $table->string('PREQ_NO')->nullable(true); // relate with BANFN
            $table->string('PREQ_ITEM')->nullable(true); // relate with BNFPO
            $table->string('TEXT_ID')->nullable(true);
            $table->string('TEXT_ID_DESC')->nullable(true);
            $table->string('TEXT_FORM')->nullable(true);
            $table->string('TEXT_LINE')->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->timestamps();
            $table->softDeletes();

            // $table->unique(['PREQ_NO','PREQ_ITEM']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sap_pr_list_item_text');
        DB::statement('DROP SEQUENCE IF EXISTS sap_pr_list_item_text_id_seq');
    }
}
