<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('vendor_approvals', function (Blueprint $table) {
            $table->bigIncrements('id'); // Auto increament
            $table->bigInteger('vendor_id'); // FK
            // main columns
            $table->string('as_position')->nullable(false);
            $table->string('approver')->nullable(false);
            $table->integer('sequence_level')->default(0)->nullable(false);
            $table->boolean('is_done')->default(false);
            
            // end main
            $table->string('created_by')->nullable(false)->default('initial')->comment('Define row who user created');
            $table->timestamps(); // created_at, updated_at
            $table->softDeletes(); // deleted_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vendor_approvals');
        DB::statement('DROP SEQUENCE IF EXISTS vendor_approvals_id_seq');
    }
}
