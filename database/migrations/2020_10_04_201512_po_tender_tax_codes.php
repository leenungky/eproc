<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PoTenderTaxCodes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('po_tax_codes', function (Blueprint $table) {
            $table->id();

            $table->string('tender_number', 32)->nullable(true);
            $table->string('pr_number')->nullable(true);
            $table->string('pr_line_number')->nullable(true);
            $table->string('tax_code', 4)->nullable(true);
            $table->string('description', 64)->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->string('deleted_by')->nullable(true);
            $table->smallInteger('action_status')->nullable(true);;
            $table->string('public_status', 30)->nullable(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['tender_number','pr_number','pr_line_number']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('po_tax_codes');
        DB::statement('DROP SEQUENCE IF EXISTS po_tender_tax_codes_id_seq');
    }
}
