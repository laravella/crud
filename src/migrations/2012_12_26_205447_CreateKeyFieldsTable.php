<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKeyFieldsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @access   public
     * @return   void
     */
    public function up()
    {
        if (!Schema::hasTable('_db_key_fields'))
        {
            Schema::create('_db_key_fields', function ($table)
                    {
                        $table->increments('id')->unique();
                        $table->integer('key_id')->unsigned()->nullable();
                        $table->integer('order')->unsigned()->nullable();
                        $table->integer('pk_field_id')->unsigned()->nullable();
                        $table->integer('pk_display_field_id')->unsigned()->nullable();
                        $table->integer('fk_field_id')->unsigned()->nullable();
                        $table->integer('fk_display_field_id')->unsigned()->nullable();
                        $table->integer('key_type_id')->unsigned()->nullable();
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
        Schema::dropIfExists('_db_key_fields');
    }

}
