<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexOtherVendor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->index('company_type_id');
            $table->index('purchase_org_id'); 
            $table->index('country'); 
            $table->index('province'); 
            $table->index('city');
            $table->index('sub_district');
            $table->index('registration_status');         
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropIndex('company_type_id');
            $table->dropIndex('purchase_org_id'); 
            $table->dropIndex('country'); 
            $table->dropIndex('province'); 
            $table->dropIndex('city');
            $table->dropIndex('sub_district');
            $table->dropIndex('registration_status');
               
        });
    }
}
