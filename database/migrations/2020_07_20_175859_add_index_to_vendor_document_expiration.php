<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexToVendorDocumentExpiration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendor_document_expiration', function (Blueprint $table) {
            $table->index('vendor_business_permits_id');
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
        Schema::table('vendor_document_expiration', function (Blueprint $table) {
            $table->dropIndex('vendor_business_permits_id');
            $table->dropIndex('vendor_profile_id');
        });
    }
}
