<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorSanctionHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('vendor_sanction_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('vendor_profile_id'); // FK
            $table->bigInteger('vendor_sanction_id'); // FK
            $table->string('username',32)->nullable(true);
            $table->string('role');
            $table->string('activity',64);
            $table->string('status',32);
            $table->string('comments');
            $table->string('pic',32)->nullable(true);
            $table->datetime('activity_date');
            $table->timestamps();
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
        Schema::dropIfExists('vendor_sanction_histories');
        DB::statement('DROP SEQUENCE IF EXISTS vendor_sanction_histories_id_seq');
    }
}
