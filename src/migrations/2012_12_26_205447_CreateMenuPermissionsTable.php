<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenuPermissionsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @access   public
     * @return   void
     */
    public function up()
    {
        if (!Schema::hasTable('_db_menu_permissions'))
        {
            Schema::create('_db_menu_permissions', function ($table)
                    {
                        $table->increments('id');
                        $table->integer('menu_id')->unsigned();
                        $table->integer('usergroup_id')->unsigned();
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
        Schema::dropIfExists('_db_menu_permissions');
    }

}
