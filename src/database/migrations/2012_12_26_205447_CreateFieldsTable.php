<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFieldsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @access   public
     * @return   void
     */
    public function up()
    {
        if (!Schema::hasTable('_db_fields'))
        {

            Schema::create('_db_fields', function ($table)
                    {
                        $table->increments('id')->unique();
                        $table->string('name', 100);                        // the field's name
                        $table->string('label', 100);                       // the label
                        $table->integer('display_type_id')->nullable();     // how the field will be displayed in lists/selects (see _db_display_types table)
                        $table->integer('searchable')->nullable();          // 1 if the field is display in a search form, else 0
                        $table->integer('display_order')->nullable();       // the order in which field will be displayed in lists/selects
                        $table->string('type', 100)->nullable();            // datatype
                        $table->integer('length')->nullable();              // datalength
                        $table->integer('width')->nullable();               // display width of the field in pixels
                        $table->integer('widget_type_id')->nullable();      // an html widget (see _db_widget_types table)
                        $table->string('null', 3)->nullable();              // nullable
                        $table->string('key', 50)->nullable();              // type of key
                        $table->string('default', 100)->nullable();         // default value
                        $table->string('extra', 100)->nullable();
                        $table->string('href', 100)->nullable();            //hyperlink this field with the href link
                        $table->integer('_db_table_id')->unsigned();        // links to _db_tables.id
                        $table->integer('pk_field_id')->unsigned();                // links to _db_fields.id (the id of the primary key)
                        $table->integer('pk_display_field_id')->unsigned();        // links to _db_fields.id (the id of a field in the primary table that will be used as a description of the primary key id)
                        $table->timestamps();

                        /*
                          $table->unique(array('_db_table_id', 'name'));
                          $table->foreign('_db_table_id')->references('id')->on('_db_tables')->onDelete('cascade');
                         */
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
        Schema::dropIfExists('_db_fields');
    }

}
