<?php namespace Laravella\Crud;

use Laravella\Crud\Log;
use \Seeder;
use \DB;

class SeedOptions extends Seeder {

    private function __addOption($optionTypeId, $name, $value)
    {
        $option = array('option_type_id' => $optionTypeId, 'name' => $name, 'value' => $value);
        $optionId = DB::table('_db_options')->insertGetId($option);
        Log::write(Log::INFO, $name . ' option created');
        return $optionId;
    }

    private function __addOptionType($name, $parentId=null)
    {
        $optionType = array('name' => $name, 'parent_id' => $parentId);
        $optionTypeId = DB::table('_db_option_types')->insertGetId($optionType);
        Log::write(Log::INFO, $name . ' option type created');
        return $optionTypeId;
    }

    public function run()
    {
        DB::table('_db_option_types')->delete();
        DB::table('_db_options')->delete();

        $optionTypeId = $this->__addOptionType('database');
        $optionTypeId = $this->__addOptionType('admin');
        $this->__addOption($optionTypeId, 'skin', 'default');
        
        $optionTypeId = $this->__addOptionType('frontend');
        $this->__addOption($optionTypeId, 'skin', 'default');
        
        $optionTypeId = $this->__addOptionType('installation');
        $this->__addOption($optionTypeId, 'status', 0);
    }

}

?>