<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenderVendorSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('tender_vendor_submissions', function (Blueprint $table) {
            $table->id();

            $table->string('tender_number', 32)->nullable(true);
            $table->bigInteger('vendor_id')->nullable(true);
            $table->datetime('submission_date')->nullable(true);
            $table->smallInteger('submission_method')->nullable(true);
            $table->string('status')->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->string('deleted_by')->nullable(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index(array('tender_number'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tender_vendor_submissions');
        DB::statement('DROP SEQUENCE IF EXISTS tender_vendor_submissions_id_seq');
    }
}
