<?php

use Laravella\Crud\Log;

class SeedDisplayTypes extends Seeder {

    private function __addDisplayType($name)
    {
        $displayTypes = array('name' => $name);
        $displayTypeId = DB::table('_db_display_types')->insertGetId($displayTypes);
        Log::write(Log::INFO, $name . ' display types created');
        return $displayTypeId;
    }

    public function run()
    {

        DB::table('_db_display_types')->delete();

        $this->__addDisplayType('edit');
        $this->__addDisplayType('display');
        $this->__addDisplayType('hidden');
        $this->__addDisplayType('nodisplay');
        $this->__addDisplayType('link');
    }

}

?>