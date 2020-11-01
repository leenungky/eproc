<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorEvaluationCriteriasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('vendor_evaluation_criterias', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('criteria_group_id');
            $table->string('name',64);
            $table->string('description')->nullable(true);
            $table->string('created_by',64)->nullable(true);
            $table->string('updated_by',64)->nullable(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index('criteria_group_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vendor_evaluation_criterias');
        DB::statement('DROP SEQUENCE IF EXISTS vendor_evaluation_criterias_id_seq');
    }
}
