<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenderParametersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('tender_parameters', function (Blueprint $table) {
            $table->id();

            $table->string('tender_number', 32)->nullable(true);
            $table->string('title')->nullable(false);
            $table->bigInteger('purchase_org_id')->unsigned()->nullable(true);
            $table->bigInteger('purchase_group_id')->unsigned()->nullable(true);
            $table->string('location')->nullable(true);
            $table->string('incoterm',32)->nullable(true);
            $table->string('tender_method',32)->nullable(true);
            $table->string('buyer')->nullable(true);
            $table->integer('prequalification')->nullable(true);
            $table->integer('eauction')->nullable(true);
            $table->string('submission_method',32)->nullable(true);
            $table->string('evaluation_method',32)->nullable(true);
            $table->string('bid_bond',32)->nullable(true);
            $table->string('winning_method',32)->nullable(true);
            $table->integer('validity_quotation')->nullable(true);
            $table->string('visibility_bid_document',32)->nullable(true);
            $table->integer('aanwijzing')->nullable(true);
            $table->string('tkdn',32)->nullable(true);
            $table->integer('tkdn_option')->nullable(true);
            $table->integer('down_payment')->nullable(true);
            $table->integer('down_payment_percentage')->nullable(true);
            $table->integer('retention')->nullable(true);
            $table->integer('retention_percentage')->nullable(true);
            $table->string('scope_of_work')->nullable(true);
            $table->string('note_to_vendor')->nullable(true);
            $table->string('note')->nullable(true);
            $table->integer('plant_id')->nullable(true);
            $table->string('status')->nullable(true);
            $table->string('workflow_status')->nullable(true);
            $table->string('workflow_values')->nullable(true);
            $table->string('retender_from',32)->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->string('deleted_by')->nullable(true);
            
            $table->timestamps();
            $table->softDeletes();

            $table->unique(array('tender_number'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tender_parameters');
        DB::statement('DROP SEQUENCE IF EXISTS tender_parameters_id_seq');
    }
}
