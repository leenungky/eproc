<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorSanctionExpirationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendor_sanction_expirations', function (Blueprint $table) {
            $table->bigIncrements('id'); // Auto increament
            $table->bigInteger('vendor_sanction_id'); // FK
            $table->bigInteger('vendor_profile_id'); // FK
            $table->string('sanction_type')->nullable(true);
            $table->date('valid_from_date')->nullable(true);
            $table->date('valid_thru_date')->nullable(true);
            $table->string('status')->nullable(true);
            $table->string('expiration_status')->nullable(true);
            
            // end main
            $table->string('created_by')->nullable(false)->default('schedule job');
            $table->string('updated_by')->nullable(false)->default('schedule job');
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
        Schema::dropIfExists('vendor_sanction_expirations');
    }
}
