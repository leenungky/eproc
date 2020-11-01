<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorProfileBankAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('vendor_profile_bank_accounts', function (Blueprint $table) {
            $table->bigIncrements('id'); // Auto increament
            $table->bigInteger('vendor_profile_id'); // FK
            // main columns
            $table->string('account_holder_name')->nullable(false);
            $table->string('account_number')->nullable(false);
            $table->string('currency')->nullable(false);
            $table->string('bank_name')->nullable(false);
            $table->string('bank_address')->nullable(false);
            $table->string('bank_statement_letter')->nullable();
            
            $table->bigInteger('parent_id')->nullable()->default(0)->unsigned();            
            $table->boolean('is_finished')->default(false)->comment('Define row status is finish changes');
            $table->boolean('is_submitted')->default(false)->comment('Define row status is submit to admin');
            $table->boolean('is_current_data')->default(false)->comment('Define row status is current data');
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
        Schema::dropIfExists('vendor_profile_bank_accounts');
        DB::statement('DROP SEQUENCE IF EXISTS vendor_profile_bank_accounts_id_seq');
    }
}
