<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorEvaluationFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('vendor_evaluation_forms', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('vendor_evaluation_id');
            $table->bigInteger('vendor_id');
            $table->integer('total_po_document')->nullable(true);
            $table->decimal('total_po_value',18,2)->nullable(true);
            $table->decimal('total_score');
            $table->string('evaluated_by');
            $table->timestamps();

            $table->index(['vendor_evaluation_id','vendor_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vendor_evaluation_forms');
        DB::statement('DROP SEQUENCE IF EXISTS vendor_evaluation_forms_id_seq');
    }
}
