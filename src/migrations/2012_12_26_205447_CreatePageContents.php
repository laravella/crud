<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePageContents extends Migration {

    /**
     * Run the migrations.
     *
     * @access   public
     * @return   void
     */
    public function up()
    {
        if (!Schema::hasTable('_db_page_contents'))
        {
            Schema::create('_db_page_contents', function ($table)
                    {
                        $table->increments('id')->unique();
                        $table->integer('content_id'); //links to _db_pages.id
                        $table->integer('page_id');
                        $table->string('slug',100);
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
        Schema::dropIfExists('_db_page_contents');
    }

}
