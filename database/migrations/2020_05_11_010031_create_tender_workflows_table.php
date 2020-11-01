<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenderWorkflowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('tender_workflows', function (Blueprint $table) {
            $table->id();
            
            $table->string('tender_number', 32)->nullable(true);
            $table->string('status', 32)->nullable(true);
            $table->string('workflow_status', 32)->nullable(true);
            $table->string('page', 32)->nullable(true);
            $table->string('user', 64)->nullable(true)->default('any');
            $table->integer('sequence')->nullable(true);
            $table->integer('is_done')->nullable(true)->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index(array('tender_number'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tender_workflows');
        DB::statement('DROP SEQUENCE IF EXISTS tender_workflows_id_seq');
    }
}
