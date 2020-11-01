<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorProfileExpertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('vendor_profile_experts', function (Blueprint $table) {
            $table->bigIncrements('id'); // Auto increament
            $table->bigInteger('vendor_profile_id'); // FK
            // main columns
            $table->string('full_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('education')->nullable();
            $table->string('university')->nullable();
            $table->string('experts_university')->nullable();
            $table->string('major')->nullable();
            $table->string('ktp_number')->nullable();
            $table->text('address')->nullable();
            $table->string('job_experience')->nullable();
            $table->integer('years_experience')->nullable();
            $table->string('certification_number')->nullable();
            $table->string('attachment')->nullable();
            
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
        Schema::dropIfExists('vendor_profile_experts');
        DB::statement('DROP SEQUENCE IF EXISTS vendor_profile_experts_id_seq');
    }
}
