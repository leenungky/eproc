<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorProfileToolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('vendor_profile_tools', function (Blueprint $table) {
            $table->bigIncrements('id'); // Auto increament
            $table->bigInteger('vendor_profile_id'); // FK
            // main columns
            $table->string('equipment_type')->nullable(false);
            $table->integer('total_qty')->nullable(false);
            $table->string('measurement')->nullable(false);
            $table->string('brand')->nullable(false);
            $table->string('condition')->nullable(false);
            $table->string('location')->nullable(false);
            $table->date('manufacturing_date')->nullable(false);
            $table->string('ownership')->nullable(false);
            
            $table->bigInteger('parent_id')->nullable()->default(0)->unsigned();            
            $table->boolean('is_finished')->default(false)->comment('Define row status is finish changes');
            $table->boolean('is_submitted')->default(false)->comment('Define row status is submit to admin');
            $table->boolean('is_current_data')->default(false)->comment('Define row status is current data');
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
        Schema::dropIfExists('vendor_profile_tools');
        DB::statement('DROP SEQUENCE IF EXISTS vendor_profile_tools_id_seq');
    }
}
