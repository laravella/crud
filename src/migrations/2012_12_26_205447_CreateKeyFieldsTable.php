<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKeyFieldsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @access   public
     * @return   void
     */
    public function up()
    {
        if (!Schema::hasTable('_db_key_fields'))
        {
            Schema::create('_db_key_fields', function ($table)
                    {
                        $table->increments('id')->unique();
                        $table->integer('field_id')->unsigned()->nullable();
                        $table->integer('key_id')->unsigned()->nullable();
                        $table->integer('order')->unsigned()->nullable();
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
        Schema::dropIfExists('_db_key_fields');
    }

}
