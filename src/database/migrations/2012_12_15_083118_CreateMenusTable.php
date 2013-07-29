<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenusTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            if (!Schema::hasTable('_db_menus'))
            {
		Schema::create('_db_menus', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('icon_class');
			$table->string('label');
			$table->string('href');
			$table->integer('parent_id')->unsigned()->default(0);
			$table->timestamps();
		});
            }
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
            Schema::dropIfExists('_db_menus');
	}

}