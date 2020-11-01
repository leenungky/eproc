<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorSanctionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('vendor_sanctions', function (Blueprint $table) {
            $table->bigIncrements('id'); // Auto increament
            $table->bigInteger('vendor_profile_id'); // FK
            $table->string('sanction_type',32);
            $table->date('valid_from_date');
            $table->date('valid_thru_date');
            $table->string('letter_number',32);
            $table->string('description');
            $table->string('attachment')->nullable(true);
            $table->string('status',16)->nullable(true); //null, approved, rejected
            $table->string('created_by',32)->nullable(true);
            $table->string('updated_by',32)->nullable(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index('vendor_profile_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vendor_sanctions');
        DB::statement('DROP SEQUENCE IF EXISTS vendor_sanctions_id_seq');
    }
}
