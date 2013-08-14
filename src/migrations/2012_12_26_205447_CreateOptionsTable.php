<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOptionsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @access   public
     * @return   void
     */
    public function up()
    {
        if (!Schema::hasTable('_db_options'))
        {
            Schema::create('_db_options', function ($table)
                    {
                        $table->increments('id')->unique();
                        $table->string('name', 100)->nullable();
                        $table->string('value', 100)->nullable();
                        $table->integer('option_type_id')->nullable();
                        $table->timestamps();
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
        Schema::dropIfExists('_db_options');
    }

}
