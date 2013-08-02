<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWidgetTypesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @access   public
     * @return   void
     */
    public function up()
    {
        if (!Schema::hasTable('_db_widget_types'))
        {
            Schema::create('_db_widget_types', function ($table)
                    {
                        $table->increments('id')->unique();
                        $table->string('name', 100);
                        $table->string('definition', 600);
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
        Schema::dropIfExists('_db_widget_types');
    }

}
