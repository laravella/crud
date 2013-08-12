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
			$table->string('icon_class')->nullable();
			$table->string('label');
			$table->integer('weight')->default(0);
			$table->string('href')->nullable();
			$table->integer('parent_id')->nullable();
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