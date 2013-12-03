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
			$table->string('href')->nullable();
			$table->integer('page_id')->unsigned()->nullable();
			$table->integer('parent_id')->nullable();
			$table->integer('weight')->default(0);
                        $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                        $table->timestamp('updated_at')->default('0000-00-00 00:00:00');
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