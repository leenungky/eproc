<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorEvaluationGeneralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('vendor_evaluation_generals', function (Blueprint $table) {
            $table->id();
            $table->string('name',64);
            $table->string('description')->nullable(true);
            $table->bigInteger('category_id');
            $table->integer('year')->nullable(true);
            $table->string('project_code')->nullable(true);
            $table->date('start_date');
            $table->date('end_date');
            $table->string('created_by',64)->nullable(true);
            $table->string('updated_by',64)->nullable(true);
            $table->string('status',32); //CONCEPT, SUBMISSION, APPROVED, REVISE
            $table->integer('revision')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vendor_evaluation_generals');
        DB::statement('DROP SEQUENCE IF EXISTS vendor_evaluation_generals_id_seq');
    }
}
