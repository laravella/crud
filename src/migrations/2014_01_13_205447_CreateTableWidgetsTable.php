<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableWidgetsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @access   public
     * @return   void
     */
    public function up()
    {
        if (!Schema::hasTable('_db_table_widgets'))
        {
            Schema::create('_db_table_widgets', function ($table)
            {
                $table->increments('id')->unique();
                $table->integer('table_id')->unsigned()->nullable();
                $table->integer('action_id')->unsigned()->nullable();
                $table->integer('display_type_id')->unsigned()->nullable();
                $table->integer('widget_type_id')->unsigned()->nullable();
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
        Schema::dropIfExists('_db_table_widgets');
    }

}
