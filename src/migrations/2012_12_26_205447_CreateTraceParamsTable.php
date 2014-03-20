<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTraceParamsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @access   public
     * @return   void
     */
    public function up()
    {
        if (!Schema::hasTable('_db_trace_params'))
        {
            Schema::create('_db_trace_params', function ($table)
                    {
                        $table->increments('id')->unique();
                        $table->integer('trace_id')->nullable();
                        $table->string('name', 200)->nullable();
                        $table->text('value')->nullable();
                    });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @access   public
     * @return   void
     */
    public function down()
    {
        Schema::dropIfExists('_db_trace_params');
    }

}
