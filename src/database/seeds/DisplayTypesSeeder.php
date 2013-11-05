<?php namespace Laravella\Crud;

use Laravella\Crud\Log;
use \Seeder;
use \DB;

/**
 * @deprecated see TablesSeeder.php
 */
class SeedDisplayTypes extends CrudSeeder {

    public function run()
    {

        DB::table('_db_display_types')->delete();

        $this->addDisplayType(1, 'nodisplay');
        $this->addDisplayType(2, 'edit');
        $this->addDisplayType(3, 'display');
        $this->addDisplayType(4, 'hidden');
        $this->addDisplayType(5, 'link');
        $this->addDisplayType(6, 'thumbnail');
        $this->addDisplayType(7, 'widget');
    }

}

?>