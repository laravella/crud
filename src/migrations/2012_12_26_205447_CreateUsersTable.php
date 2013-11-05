<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class xxxxCreateUsersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @access   public
     * @return   void
     */
    public function up()
    {
        // Create the users table.
        //
        if (!Schema::hasTable('users'))
        {
            Schema::create('users', function($table)
                    {
                        $table->increments('id');
			$table->string('username',100)->unique();
                        $table->string('email', 100);
                        $table->string('password', 100);
                        $table->string('first_name', 100);
                        $table->string('last_name', 100);
                        $table->string('api_token');
                        $table->integer('activated');
                        $table->integer('usergroup_id');
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
        Schema::dropIfExists('users');
    }

}
