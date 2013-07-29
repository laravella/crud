<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableActionViewsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @access   public
	 * @return   void
	 */
	public function up()
	{
        Schema::create('_db_table_action_views', function ($table)
                {
                    $table->increments('id');
                    $table->integer('table_id')->unsigned();
                    $table->integer('action_id')->unsigned();
                    $table->integer('view_id')->unsigned();
                    $table->integer('page_size')->unsigned(); //the size of a page (pagination) in a list view
                    $table->string('title',50);
                    $table->timestamps();                    
/*
                    $table->foreign('view_id')->references('id')->on('_db_views')->onDelete('cascade');
                    $table->foreign('table_id')->references('id')->on('_db_tables')->onDelete('cascade');
                    $table->foreign('action_id')->references('id')->on('_db_actions')->onDelete('cascade'); */
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
		Schema::drop('_db_table_action_views');
	}
}
