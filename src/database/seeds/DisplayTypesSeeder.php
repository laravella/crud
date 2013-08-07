<?php

use Laravella\Crud\Log;

class SeedDisplayTypes extends Seeder {

    private function __addDisplayType($id, $name)
    {
        $displayTypes = array('id' => $id, 'name' => $name);
        DB::table('_db_display_types')->insert($displayTypes);
        Log::write(Log::INFO, $name . ' display types created');
    }

    public function run()
    {

        DB::table('_db_display_types')->delete();

        $this->__addDisplayType(1, 'nodisplay');
        $this->__addDisplayType(2, 'edit');
        $this->__addDisplayType(3, 'display');
        $this->__addDisplayType(4, 'hidden');
        $this->__addDisplayType(5, 'link');
    }

}

?>