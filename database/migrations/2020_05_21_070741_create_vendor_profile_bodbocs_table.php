<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorProfileBodbocsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('vendor_profile_bodbocs', function (Blueprint $table) {
            $table->bigIncrements('id'); // Auto increament
            $table->bigInteger('vendor_profile_id'); // FK
            // main columns
            $table->enum('board_type', ['BOD (Board of Director)','BOC (Board of Commisioner)'])->nullable(false);
            $table->boolean('is_person_company_shareholder')->default(true)->nullable(false);
            $table->string('full_name')->nullable(false);
            $table->enum('nationality', ['WNI','WNA'])->nullable();
            $table->string('position')->nullable();
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->boolean('company_head')->default(true)->nullable(false);
            
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
        Schema::dropIfExists('vendor_profile_bodbocs');
        DB::statement('DROP SEQUENCE IF EXISTS vendor_profile_bodbocs_id_seq');
    }
}
