<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePageMembersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @access   public
     * @return   void
     */
    public function up()
    {
        if (!Schema::hasTable('_db_page_members'))
        {
            Schema::create('_db_page_members', function ($table)
                    {
                        $table->increments('id')->unique();
                        $table->string('name', 100)->unique();
                        $table->integer('member_type')->unsigned();  //link to _db_optio_types where parent is member-types
                        $table->text('code');
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
        Schema::dropIfExists('_db_page_members');
    }

}
