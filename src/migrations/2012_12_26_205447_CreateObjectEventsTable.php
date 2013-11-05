<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateObjectEventsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @access   public
     * @return   void
     */
    public function up()
    {
        if (!Schema::hasTable('_db_object_events'))
        {
            Schema::create('_db_object_events', function ($table)
                    {
                        $table->increments('id')->unique();
                        $table->integer('object_id'); //links to _db_pages.id
                        $table->integer('event_id');
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
        Schema::dropIfExists('_db_object_events');
    }

}
