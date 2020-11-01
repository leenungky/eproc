<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorProfileDetailStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('vendor_profile_detail_statuses', function (Blueprint $table) {
            $table->bigIncrements('id'); // Auto increament
            $table->bigInteger('vendor_profile_id'); // FK
            // main columns
            // IS FINISH CHANGES
            $tableFields = [
                'general',
                'deed',
                'shareholder',
                'bodboc',
                'businesspermit',
                'pic',
                'equipment',
                'expert',
                'certification',
                'scopeofsupply',
                'experience',
                'bankaccount',
                'financial',
                'tax'
            ];
            foreach($tableFields as $field){
                $table->enum($field . '_status',['none','finish','warning'])->default('none')->nullable(false);
            }
            $table->string('update_vendor_data_status')->default(null)->nullable()->comment('Define Update vendor data status');
            
            $table->boolean('is_submitted')->default(false)->nullable(false)->comment('Define row profile has submitted by vendor');
            $table->boolean('is_approved')->default(false)->nullable(false)->comment('Define row admin approve vendor profile');
            $table->boolean('is_revised')->default(false)->nullable(false)->comment('Define row admin revise vendor profile');
            
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
        Schema::dropIfExists('vendor_profile_detail_statuses');
        DB::statement('DROP SEQUENCE IF EXISTS vendor_profile_detail_statuses_id_seq');
    }
}
