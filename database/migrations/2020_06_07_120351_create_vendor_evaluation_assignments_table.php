<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorEvaluationAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('vendor_evaluation_assignments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('vendor_evaluation_id'); // FK
            $table->bigInteger('criteria_id'); // FK
            $table->integer('weighting');
            $table->integer('minimum_score');
            $table->integer('maximum_score');
            $table->integer('sequence');
            $table->timestamps();
            $table->index('vendor_evaluation_id');
            $table->unique(['vendor_evaluation_id','criteria_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vendor_evaluation_assignments');
        DB::statement('DROP SEQUENCE IF EXISTS vendor_evaluation_assignments_id_seq');
    }
}
