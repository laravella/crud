<?php namespace Laravella\Crud;

use Laravella\Crud\Log;
use \Seeder;
use \DB;


class SeedWidgetTypes extends Seeder {

    private function __addWidgetType($name)
    {
        $widgetTypeId = DB::table('_db_widget_types')->insertGetId(array('name' => $name));
        Log::write(Log::INFO, $name . ' widget type created');
        return $widgetTypeId;
    }

    public function run()
    {

        DB::table('_db_widget_types')->delete();

        $this->__addWidgetType('input:text');
        $this->__addWidgetType('input:hidden');
        $this->__addWidgetType('input:text');
        $this->__addWidgetType('input:checkbox');
        $this->__addWidgetType('input:radio');
        $this->__addWidgetType('textarea');
        $this->__addWidgetType('select');
        $this->__addWidgetType('multiselect');
        $this->__addWidgetType('ckeditor');
        $this->__addWidgetType('span');
        $this->__addWidgetType('password');
        $this->__addWidgetType('password:hashed');
        $this->__addWidgetType('password:md5');
    }

}

?>