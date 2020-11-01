<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePoVendorProfile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('po_vendor_profile', function (Blueprint $table) {
            $table->bigIncrements('id'); // Auto increament
            $table->bigInteger('vendor_profile_id'); // FK
            // main columns
            $table->string('vendor_code',16)->nullable(true);
            $table->string('company_name')->nullable(false);
            $table->bigInteger('company_type_id')->nullable(false)->unsigned();
            $table->string('location_category')->nullable();            
            $table->string('country', 200)->nullable(false);
            $table->string('province', 200)->nullable(true);
            $table->string('city', 200)->nullable(true);
            $table->string('sub_district', 200)->nullable(true);
            $table->string('postal_code', 20)->nullable(false);
            $table->string('address_1', 255)->nullable(true);
            $table->string('address_2', 255)->nullable(true);
            $table->string('address_3', 255)->nullable(true);
            $table->string('address_4', 255)->nullable(true);
            $table->string('address_5', 255)->nullable(true);
            $table->string('phone_number', 32)->nullable(false);
            $table->string('fax_number', 32)->nullable();
            $table->string('website')->nullable();
            $table->string('company_email', 100)->nullable(true);
            $table->string('rt', 100)->nullable(true);
            $table->string('rw', 100)->nullable(true);
            $table->string('street')->nullable(true);
            $table->string('building_name')->nullable(true);
            $table->string('kavling_floor_number')->nullable(true);
            $table->string('village')->nullable(true);
            $table->string('house_number')->nullable(true);
            $table->bigInteger('parent_id')->nullable()->default(0)->unsigned();            
            $table->boolean('primary_data')->default(false)->comment('Define row to show vendor profile info');
            $table->boolean('is_finished')->default(false)->comment('Define row status is finish changes');
            $table->boolean('is_submitted')->default(false)->comment('Define row status is submit to admin');
            $table->boolean('is_current_data')->default(false)->comment('Define row status is current data');
            // end main
            $table->string('created_by')->nullable(false)->default('initial')->comment('Define row who user created');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('po_vendor_profile');
    }
}
