<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorProfileExperienceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('vendor_profile_experience', function (Blueprint $table) {
            $table->bigIncrements('id'); // Auto increament
            $table->bigInteger('vendor_profile_id'); // FK
            // main columns
            $table->string('classification')->nullable(false);
            $table->string('sub_classification')->nullable(true);
            $table->string('project_name')->nullable(false);
            $table->string('project_location')->nullable(false);
            $table->string('contract_owner')->nullable(false);
            $table->string('address')->nullable();
            $table->string('country')->nullable();
            $table->string('province')->nullable();
            $table->string('city')->nullable();
            $table->string('sub_district')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('contract_number')->nullable(false);
            $table->string('valid_from_date')->nullable(false);
            $table->string('valid_thru_date')->nullable(false);
            $table->string('currency')->nullable();
            $table->string('contract_value')->nullable();
            $table->string('bast_wan_date')->nullable();
            $table->string('bast_wan_number')->nullable();
            $table->string('bast_wan_attachment')->nullable();
            
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
        Schema::dropIfExists('vendor_profile_experience');
        DB::statement('DROP SEQUENCE IF EXISTS vendor_profile_experience_id_seq');
    }
}
