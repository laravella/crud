<?php namespace Laravella\Crud;

use Laravella\Crud\Log;
use \Seeder;
use \DB;


/**
 * @deprecated see TablesSeeder.php
 */
class SeedWidgetTypes extends CrudSeeder {

    public function run()
    {

        DB::table('_db_widget_types')->delete();

        $this->addWidgetType('input:text');
        $this->addWidgetType('input:hidden');
        $this->addWidgetType('input:text');
        $this->addWidgetType('input:checkbox');
        $this->addWidgetType('input:radio');
        $this->addWidgetType('textarea');
        $this->addWidgetType('select');
        $this->addWidgetType('multiselect');
        $this->addWidgetType('ckeditor');
        $this->addWidgetType('span');
        $this->addWidgetType('password');
        $this->addWidgetType('password:hashed');
        $this->addWidgetType('password:md5');
        $this->addWidgetType('thumbnail');
    }

}

?>