<?php namespace Laravella\Crud;

use Laravella\Crud\Log;
use \Seeder;
use \DB;

/**
 * @deprecated see TablesSeeder.php
 */
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
        $this->__addDisplayType(6, 'thumbnail');
        $this->__addDisplayType(7, 'widget');
    }

}

?>