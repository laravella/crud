<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePageAssetsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @access   public
     * @return   void
     */
    public function up()
    {
        if (!Schema::hasTable('_db_page_assets'))
        {
            Schema::create('_db_page_assets', function ($table)
                    {
                        $table->increments('id')->unique();
                        $table->integer('page_id'); //links to _db_pages.id
                        $table->integer('asset_id');
                        $table->string('position'); //top or bottom
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
        Schema::dropIfExists('_db_page_assets');
    }

}
