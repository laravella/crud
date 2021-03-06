<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssetsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @access   public
     * @return   void
     */
    public function up()
    {
        if (!Schema::hasTable('_db_assets'))
        {
            Schema::create('_db_assets', function ($table)
                    {
                        $table->increments('id')->unique();
                        $table->string('url', 200);
                        $table->string('vendor', 100);
                        $table->integer('asset_type_id')->unsigned(); //links to _db_option_types
                        $table->string('type', 100); //scripts, styles, images, fonts, as a subfolder of the skin
                        $table->string('version', 20);
                        $table->string('position'); //top or bottom of the page
                        $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                        $table->timestamp('updated_at')->default('0000-00-00 00:00:00');
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
        Schema::dropIfExists('_db_assets');
    }

}
