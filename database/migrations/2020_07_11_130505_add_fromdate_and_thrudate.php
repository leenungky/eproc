<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFromdateAndThrudate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendor_document_expiration', function (Blueprint $table) {
            $table->date('valid_from_date')->nullable();
            $table->date('valid_thru_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendor_document_expiration', function (Blueprint $table) {
            $table->dropColumn('valid_from_date');
            $table->dropColumn('valid_thru_date');
        });
    }
}
