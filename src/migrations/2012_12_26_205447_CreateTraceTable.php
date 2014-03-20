<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTraceTable extends Migration {

    /**
     * Run the migrations.
     *
     * @access   public
     * @return   void
     */
    public function up()
    {
        if (!Schema::hasTable('_db_trace'))
        {
            Schema::create('_db_trace', function ($table)
                    {
                        $table->increments('id')->unique();
                        $table->string('method', 100)->nullable();
                        $table->string('filename', 300)->nullable();
                        $table->integer('line')->nullable();
                        $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                        $table->timestamp('updated_at')->default('0000-00-00 00:00:00');
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
        Schema::dropIfExists('_db_trace');
    }

}
