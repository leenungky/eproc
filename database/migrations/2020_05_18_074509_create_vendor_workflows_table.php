<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorWorkflowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('vendor_workflows', function (Blueprint $table) {
            $table->bigIncrements('id'); // Auto increament
            $table->bigInteger('vendor_id')->nullable(); // FK
            $table->string('activity');
            $table->string('remarks')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            // end maintimestamp
            $table->string('created_by')->nullable(false)->default('system')->comment('Define row who user created'); // FK
            $table->timestamps(); // created_at, updated_at
            $table->softDeletes(); // deleted_at
            
            $table->index(array('vendor_id','created_by'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vendor_workflows');
        DB::statement('DROP SEQUENCE IF EXISTS vendor_workflows_id_seq');
    }
}
