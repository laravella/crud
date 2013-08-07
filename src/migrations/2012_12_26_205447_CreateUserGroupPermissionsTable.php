<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserGroupPermissionsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @access   public
     * @return   void
     */
    public function up()
    {
        if (!Schema::hasTable('_db_usergroup_permissions'))
        {
            Schema::create('_db_usergroup_permissions', function ($table)
                    {
                        $table->increments('id')->unique();
                        $table->integer('usergroup_id')->unsigned();
                        $table->integer('table_id')->unsigned();
                        $table->integer('action_id')->unsigned();
                        $table->timestamps();

                        /*
                          $table->foreign('usergroup_id')->references('id')->on('usergroups')->onDelete('cascade');
                          $table->foreign('table_id')->references('id')->on('_db_tables')->onDelete('cascade');
                          $table->foreign('action_id')->references('id')->on('_db_actions')->onDelete('cascade');
                         */
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
        Schema::dropIfExists('_db_usergroup_permissions');
    }

}
