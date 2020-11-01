<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorHistoryStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('vendor_history_statuses', function (Blueprint $table) {
            $table->bigIncrements('id'); // Auto increament
            $table->bigInteger('vendor_id'); // FK
            // main columns
            $table->string('status')->nullable(false);
            $table->string('description')->nullable();
            $table->string('version')->nullable();
            // $table->string('process')->nullable();
            $table->string('remarks')->nullable();
            
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
        Schema::dropIfExists('vendor_history_statuses');
        DB::statement('DROP SEQUENCE IF EXISTS vendor_history_statuses_id_seq');
    }
}
