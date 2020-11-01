<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorProfileBusinessPermitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('vendor_profile_business_permits', function (Blueprint $table) {
            $table->bigIncrements('id'); // Auto increament
            $table->bigInteger('vendor_profile_id'); // FK
            // main columns
            $table->enum('business_permit_type', [
                'Surat Izin Usaha Perdagangan',
                'SKT Migas',
                'Tanda Daftar Perusahaan',
                'Surat Izin Tempat Usaha',
                'Surat Keterangan Domisili',
                'Surat Izin Pelayaran',
                'Surat Izin Depnaker',
                'Surat Izin Usaha Jasa Konstruksi'
            ])->nullable(false);
            $table->enum('business_class', ['Small','Medium','Large'])->nullable(false);
            $table->string('business_permit_number')->nullable(false);
            $table->date('valid_from_date')->nullable(false);
            $table->date('valid_thru_date')->nullable(false);
            $table->string('issued_by')->nullable(false);
            $table->string('attachment')->nullable(false);
            
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
        Schema::dropIfExists('vendor_profile_business_permits');
        DB::statement('DROP SEQUENCE IF EXISTS vendor_profile_business_permits_id_seq');
    }
}
