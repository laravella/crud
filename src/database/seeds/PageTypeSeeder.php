<?php namespace Laravella\Crud;

use Laravella\Crud\Log;
use \Seeder;
use \DB;

class SeedPageTypes extends CrudSeeder {

    public function run()
    {
        
        $skin = "flatly";
        
        $this->addPageType("skins::$skin.dbview");
        $this->addPageType("skins::$skin.frontview");
    }

}

?>