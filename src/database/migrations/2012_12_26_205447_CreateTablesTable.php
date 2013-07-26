<?php

use Illuminate\Database\Migrations\Migration;

class CreateTablesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @access   public
	 * @return   void
	 */
	public function up()
	{
            Schema::create('_db_tables', function ($table)
                {
                    $table->increments('id')->unique();
                    $table->string('name', 100)->unique();
                    $table->timestamps();                    
                    
                });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @access   public
	 * @return   void
	 */
	public function down()
	{
		Schema::drop('_db_tables');
	}
}
