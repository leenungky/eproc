<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('vendor_profiles', function (Blueprint $table) {
            $table->bigIncrements('id'); // Auto increament
            $table->bigInteger('vendor_id'); // FK
            // main columns
            $table->string('company_name')->nullable(false); // inserted from admin approve applicant to candidates
            $table->string('company_type')->nullable(false);
            $table->string('company_category')->nullable();
            $table->enum('company_status', ['ACTIVE','NON ACTIVE'])->nullable();
            $table->string('active_skl_number')->nullable();
            $table->string('active_skl_attachment')->nullable();
            $table->string('company_warning')->nullable();                 
            
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
        Schema::dropIfExists('vendor_profiles');
        DB::statement('DROP SEQUENCE IF EXISTS vendor_profiles_id_seq');
    }
}
