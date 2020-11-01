<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorProfileGeneralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('vendor_profile_generals', function (Blueprint $table) {
            $table->bigIncrements('id'); // Auto increament
            $table->bigInteger('vendor_profile_id'); // FK
            // main columns
            $table->string('company_name')->nullable(false);
            $table->bigInteger('company_type_id')->nullable(false)->unsigned();
            $table->string('location_category')->nullable();
            $table->string('country', 200)->nullable(false);
            $table->string('province', 200)->nullable(false);
            $table->string('city', 200)->nullable(false);
            $table->string('sub_district', 200)->nullable(false);
            $table->string('postal_code', 20)->nullable(false);
            $table->string('address_1', 255)->nullable(false);
            $table->string('address_2', 255)->nullable(false);
            $table->string('address_3', 255)->nullable(false);
            $table->string('address_4', 255)->nullable(false);
            $table->string('address_5', 255)->nullable(false);
            $table->string('phone_number', 32)->nullable(false);
            $table->string('fax_number', 32)->nullable();
            $table->string('website')->nullable();
            $table->string('company_email', 100)->nullable(true);
            
            $table->bigInteger('parent_id')->nullable()->default(0)->unsigned();            
            $table->boolean('primary_data')->default(false)->comment('Define row to show vendor profile info');
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
        Schema::dropIfExists('vendor_profile_generals');
        DB::statement('DROP SEQUENCE IF EXISTS vendor_profile_generals_id_seq');
    }
}
