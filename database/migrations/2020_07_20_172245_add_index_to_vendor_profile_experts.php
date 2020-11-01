<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexToVendorProfileExperts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendor_profile_experts', function (Blueprint $table) {
            $table->index('vendor_profile_id');
            $table->index('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendor_profile_experts', function (Blueprint $table) {
            $table->dropIndex('vendor_profile_id');
            $table->dropIndex('parent_id');
        });
    }
}
