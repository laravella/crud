<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @access   public
     * @return   void
     */
    public function up()
    {
        if (!Schema::hasTable('_db_pages'))
        {
            Schema::create('_db_pages', function ($table)
                    {
                        $table->increments('id');
                        $table->integer('table_id')->unsigned();
                        $table->integer('action_id')->unsigned();
                        $table->integer('view_id')->unsigned();
                        $table->integer('object_id')->unsigned();
                        $table->integer('page_type_id')->unsigned();
                        $table->integer('page_size')->unsigned(); //the size of a page (pagination) in a list view
                        $table->string('title', 50);
                        $table->string('slug', 50);
                        $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                        $table->timestamp('updated_at')->default('0000-00-00 00:00:00');
                        /*
                          $table->foreign('view_id')->references('id')->on('_db_views')->onDelete('cascade');
                          $table->foreign('table_id')->references('id')->on('_db_tables')->onDelete('cascade');
                          $table->foreign('action_id')->references('id')->on('_db_actions')->onDelete('cascade'); */
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
        Schema::dropIfExists('_db_pages');
    }

}
