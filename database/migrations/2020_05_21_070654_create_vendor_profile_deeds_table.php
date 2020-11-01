<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorProfileDeedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('vendor_profile_deeds', function (Blueprint $table) {
            $table->bigIncrements('id'); // Auto increament
            $table->bigInteger('vendor_profile_id'); // FK
            // main columns
            $table->enum('deed_type', ['Deed of Establishment (Akta Pendirian)','Last Updated of Company Deed (Akta Perubahan)'])->nullable(false);
            $table->string('deed_number')->nullable(false);
            $table->date('deed_date')->nullable(false);
            $table->string('notary_name')->nullable(false);
            $table->string('sk_menkumham_number')->nullable(false);
            $table->date('sk_menkumham_date')->nullable(false);
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
        Schema::dropIfExists('vendor_profile_deeds');
        DB::statement('DROP SEQUENCE IF EXISTS vendor_profile_deeds_id_seq');
    }
}
