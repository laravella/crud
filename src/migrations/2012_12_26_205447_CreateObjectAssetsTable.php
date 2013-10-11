<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateObjectAssetsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @access   public
     * @return   void
     */
    public function up()
    {
        if (!Schema::hasTable('_db_object_assets'))
        {
            Schema::create('_db_object_assets', function ($table)
                    {
                        $table->increments('id')->unique();
                        $table->integer('object_id');
                        $table->integer('asset_id');
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
        Schema::dropIfExists('_db_object_assets');
    }

}
