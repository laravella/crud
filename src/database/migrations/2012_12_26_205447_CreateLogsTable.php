<?php

use Illuminate\Database\Migrations\Migration;

class CreateLogsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @access   public
	 * @return   void
	 */
	public function up()
	{
        Schema::create('_db_logs', function ($table)
                {
                    $table->increments('id')->unique();
                    $table->integer('severity');
                    $table->string('message', 100);
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
		Schema::drop('_db_logs');
	}
}
