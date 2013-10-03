<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKeysTable extends Migration {

    /**
     * Run the migrations.
     *
     * @access   public
     * @return   void
     */
    public function up()
    {
        if (!Schema::hasTable('_db_keys'))
        {
            Schema::create('_db_keys', function ($table)
                    {
                        $table->increments('id')->unique();
                        $table->string('name', 100);
                        $table->integer('field_id')->unsigned()->nullable();
                        $table->integer('key_type_id')->unsigned()->nullable();
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
        Schema::dropIfExists('_db_keys');
    }

}
