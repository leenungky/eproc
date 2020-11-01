<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenderAanwijzingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('tender_aanwijzings', function (Blueprint $table) {
            $table->id();

            $table->string('tender_number', 32)->nullable(true);
            $table->string('event_name')->nullable(true);
            $table->string('venue')->nullable(true);
            $table->datetime('event_start')->nullable(true);
            $table->datetime('event_end')->nullable(true);
            $table->string('note')->nullable(true);
            $table->string('status')->nullable(true)->default('DRAFT');
            $table->string('result_attachment')->nullable(true);
            $table->string('result_description')->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->string('deleted_by')->nullable(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index(array('tender_number'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tender_aanwijzings');
        DB::statement('DROP SEQUENCE IF EXISTS tender_aanwijzings_id_seq');
    }
}
