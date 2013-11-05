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
		Schema::dropIfExists('mcollection_media');
	}

}
