<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class xxxxAddMcollectionMediaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            if (!Schema::hasTable('mcollection_media'))
            {
                    Schema::create('mcollection_media', function(Blueprint $table)
                    {
                            $table->increments('id');
                            $table->integer('media_id');
                            $table->integer('mcollection_id');
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
		Schema::dropIfExists('mcollection_media');
	}

}
