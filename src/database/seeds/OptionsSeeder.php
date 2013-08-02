<?php

use Laravella\Crud\Log;

class SeedOptions extends Seeder {

    private function __addOption($optionTypeId, $name, $value)
    {
        $option = array('option_type_id' => $optionTypeId, 'name' => $name, 'value' => $value);
        $optionId = DB::table('_db_options')->insertGetId($option);
        Log::write(Log::INFO, $name . ' option created');
        return $optionId;
    }

    public function run()
    {
        DB::table('_db_option_types')->delete();
        DB::table('_db_options')->delete();

        $optionTypeId = DB::table('_db_option_types')->insertGetId(array('name'=>'installation'));

        $this->__addOption($optionTypeId, 'status', 0);
    }

}

?>