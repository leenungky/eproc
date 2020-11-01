<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorProfileBusinessPermitsStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('vendor_profile_business_permits_status', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('vendor_business_permits_id'); 
            $table->bigInteger('vendor_profile_id');
            $table->string('status')->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vendor_profile_business_permits_status');
        DB::statement('DROP SEQUENCE IF EXISTS vendor_profile_business_permits_status_id_seq');
    }
}
