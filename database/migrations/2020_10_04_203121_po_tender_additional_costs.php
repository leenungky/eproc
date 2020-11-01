<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PoTenderAdditionalCosts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('po_additional_costs', function (Blueprint $table) {
            $table->id();

            $table->string('tender_number', 32)->nullable(true);
            $table->string('pr_number')->nullable(true);
            $table->string('pr_line_number')->nullable(true);
            $table->string('conditional_code', 6)->nullable(true);
            $table->string('conditional_name', 64)->nullable(true);
            $table->decimal('percentage', 5,2)->nullable(true);
            $table->decimal('value')->nullable(true);
            $table->smallInteger('calculation_pos')->nullable(true);
            $table->string('conditional_type', 64)->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->string('deleted_by')->nullable(true);
            $table->smallInteger('action_status');
            $table->string('public_status')->nullable(true);;
            $table->string('line_id', 30)->nullable(true);;
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tender_number','pr_number','pr_line_number']);
            $table->index(['tender_number','conditional_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('po_additional_costs');
        DB::statement('DROP SEQUENCE IF EXISTS po_tender_additional_costs_id_seq');
    }
}
