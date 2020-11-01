<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorEvaluationCriteriaGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('vendor_evaluation_criteria_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name',64);
            $table->string('created_by',64)->nullable(true);
            $table->string('updated_by',64)->nullable(true);
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
        Schema::dropIfExists('vendor_evaluation_criteria_groups');
        DB::statement('DROP SEQUENCE IF EXISTS vendor_evaluation_criteria_groups_id_seq');
    }
}
