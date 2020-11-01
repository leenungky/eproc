<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexToVendorProfileGeneral extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendor_profile_generals', function (Blueprint $table) {
            $table->index('vendor_profile_id');
            $table->index('company_type_id');
            $table->index('country');
            $table->index('province');
            $table->index('city');
            $table->index('sub_district');
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
        Schema::table('vendor_profile_generals', function (Blueprint $table) {
            $table->dropIndex('vendor_profile_id');
            $table->dropIndex('company_type_id');
            $table->dropIndex('country');
            $table->dropIndex('province');
            $table->dropIndex('city');
            $table->dropIndex('sub_district');
            $table->dropIndex('parent_id'); 
        });
    }
}
