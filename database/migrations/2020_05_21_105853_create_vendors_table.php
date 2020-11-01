<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('vendors', function (Blueprint $table) {
            $table->bigIncrements('id'); // Auto increament
            // main columns
            $table->string('vendor_group', 100)->nullable(false);                        
            $table->string('vendor_name', 255)->nullable(false);                        
            $table->bigInteger('company_type_id')->nullable(false)->unsigned();
            $table->bigInteger('purchase_org_id')->nullable(false)->unsigned();
            $table->string('president_director', 200)->nullable(false);
            $table->string('address_1', 255)->nullable(false);
            $table->string('address_2', 255)->nullable();
            $table->string('address_3', 255)->nullable();
            $table->string('address_4', 255)->nullable();
            $table->string('address_5', 255)->nullable();
            $table->string('country', 200)->nullable(false);
            $table->string('province', 200)->nullable(false);
            $table->string('city', 200)->nullable(false);
            $table->string('sub_district', 200)->nullable(false);
            $table->string('house_number', 20)->nullable();
            $table->string('postal_code', 20)->nullable(false);
            $table->string('phone_number', 32)->nullable(false);
            $table->string('fax_number', 32)->nullable();
            $table->string('company_email', 255)->nullable();
            $table->string('company_site', 255)->nullable();
            $table->string('pic_full_name', 200)->nullable(false);
            $table->string('pic_mobile_number', 20)->nullable(false);
            $table->string('pic_email', 255)->nullable(false);
            $table->string('tender_ref_number', 255)->nullable();
            $table->string('identification_type', 100)->nullable();
            $table->string('idcard_number', 100)->nullable();
            $table->string('idcard_attachment', 100)->nullable();
            $table->string('tin_number', 100)->nullable();
            $table->string('tin_attachment', 255)->nullable();
            $table->string('pkp_type', 100)->nullable();
            $table->string('pkp_number', 100)->nullable();
            $table->string('pkp_attachment', 255)->nullable();
            $table->string('non_pkp_number', 100)->nullable();
            $table->string('vendor_code', 16)->nullable();       
            $table->string('business_partner_code', 255)->nullable();            
            $table->string('sap_vendor_code', 255)->nullable();            
            $table->boolean('already_exist_sap')->default(false)->nullable();            
            $table->string('registration_status')->default('applicant')->nullable();            
            // end main
            $table->string('created_by')->nullable(false)->default('applicant')->comment('Define row who user created');
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
        Schema::dropIfExists('vendors');
        DB::statement('DROP SEQUENCE IF EXISTS vendors_id_seq');
    }
}
