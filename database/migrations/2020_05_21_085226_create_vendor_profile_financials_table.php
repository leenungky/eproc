<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorProfileFinancialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('vendor_profile_financials', function (Blueprint $table) {
            $table->bigIncrements('id'); // Auto increament
            $table->bigInteger('vendor_profile_id'); // FK
            // main columns
            $table->date('financial_statement_date')->nullable(false);
            $table->string('public_accountant_full_name')->nullable(false);
            $table->enum('audit', ['Audited', 'Non Audited'])->nullable(false);
            $table->string('financial_statement_year', 4)->nullable(false);
            $table->date('valid_thru_date')->nullable(false);
            $table->string('financial_statement_attachment')->nullable();
            $table->string('currency')->nullable();
            // Balance Sheet (input)
            $table->decimal('cash', 18, 2)->nullable(false);
            $table->decimal('bank', 18, 2)->nullable();
            $table->decimal('short_term_investments', 18, 2)->nullable();
            $table->decimal('long_term_investments', 18, 2)->nullable();
            $table->decimal('total_receivables', 18, 2)->nullable();
            $table->decimal('inventories', 18, 2)->nullable();
            $table->decimal('work_in_progress', 18, 2)->nullable(false);
            $table->decimal('total_current_assets', 18, 2)->nullable(false);
            $table->decimal('equipments_and_machineries', 18, 2)->nullable(false);
            $table->decimal('fixed_inventories', 18, 2)->nullable();
            $table->decimal('buildings', 18, 2)->nullable();
            $table->decimal('lands', 18, 2)->nullable();
            $table->decimal('total_fixed_assets', 18, 2)->nullable();
            $table->decimal('other_assets', 18, 2)->nullable();
            $table->decimal('incoming_debts', 18, 2)->nullable();
            $table->decimal('taxes_payables', 18, 2)->nullable();
            $table->decimal('other_payables', 18, 2)->nullable();
            $table->decimal('total_short_term_debts', 18, 2)->nullable();
            $table->decimal('long_term_payables', 18, 2)->nullable();
            $table->decimal('total_net_worth', 18, 2)->nullable();
            $table->decimal('total_assets', 18, 2)->nullable();
            $table->decimal('total_liabilities', 18, 2)->nullable();
            $table->decimal('total_net_worth_exclude_land_building', 18, 2)->nullable();
            $table->decimal('annual_revenue', 18, 2)->nullable();
            $table->enum('business_class', ['Small','Medium','Large'])->nullable();
            
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
        Schema::dropIfExists('vendor_profile_financials');
        DB::statement('DROP SEQUENCE IF EXISTS vendor_profile_financials_id_seq');
    }
}
