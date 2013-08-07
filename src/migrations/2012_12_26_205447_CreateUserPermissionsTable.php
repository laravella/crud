<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserPermissionsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @access   public
     * @return   void
     */
    public function up()
    {
        if (!Schema::hasTable('_db_user_permissions'))
        {
            Schema::create('_db_user_permissions', function ($table)
                    {
                        $table->increments('id')->unique();
                        $table->integer('user_id')->unsigned();
                        $table->integer('table_id')->unsigned();
                        $table->integer('action_id')->unsigned();
                        $table->timestamps();
                        /*
                          $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('_db_user_permissions');
    }

}
