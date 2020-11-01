<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenderDocumentType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('tender_document_type', function (Blueprint $table) {
            $table->id();
            $table->string("tender_number")->nullable(false);
            $table->string("vendor_code")->nullable(false);
            $table->string("document_type")->nullable(true);
            $table->dateTime("document_date")->nullable(true);
            $table->dateTime("delivery_date")->nullable(true);
            $table->dateTime("deleted_at")->nullable(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tender_document_type');
    }
}
