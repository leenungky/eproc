<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTenderAdditionalCostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('tender_additional_costs', function (Blueprint $table) {
            $table->id();

            $table->string('tender_number', 32)->nullable(true);
            $table->string('pr_number')->nullable(true);
            $table->string('pr_line_number')->nullable(true);
            $table->string('conditional_code', 6)->nullable(true);
            $table->string('conditional_name', 64)->nullable(true);
            $table->integer('percentage')->nullable(true);
            $table->decimal('value')->nullable(true);
            $table->smallInteger('calculation_pos')->nullable(true);
            $table->string('conditional_type', 64)->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->string('deleted_by')->nullable(true);
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
        Schema::dropIfExists('tender_additional_costs');
        DB::statement('DROP SEQUENCE IF EXISTS tender_additional_costs_id_seq');
    }
}
