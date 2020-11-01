<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRefPurchaseGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('ref_purchase_groups', function (Blueprint $table) {
            $table->bigIncrements('id'); // Auto increament
            // main columns
            $table->char('group_code', 3)->nullable(false);
            $table->string('description', 50)->nullable(false);          
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
        Schema::dropIfExists('ref_purchase_groups');
        DB::statement('DROP SEQUENCE IF EXISTS ref_purchase_groups_id_seq');
    }
}