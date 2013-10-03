<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKeyTypesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @access   public
     * @return   void
     */
    public function up()
    {
        if (!Schema::hasTable('_db_key_types'))
        {
            Schema::create('_db_key_types', function ($table)
                    {
                        $table->increments('id')->unique();
                        $table->string('name', 100);
                        $table->integer('levels')->unsigned()->nullable();
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
        Schema::dropIfExists('_db_key_types');
    }

}
