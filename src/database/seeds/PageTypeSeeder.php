<?php namespace Laravella\Crud;

use Laravella\Crud\Log;
use \Seeder;
use \DB;
use \Config;

class SeedPageTypes extends CrudSeeder {

    public function run()
    {
        
        $skins = Config::get('app.skins');//"flatly";
        
        $this->addPageType("{$skins['admin']}.dbview");
        $this->addPageType("{$skins['frontend']}.frontview");
        $this->addPageType("{$skins['admin']}.login");
        $this->addPageType("{$skins['admin']}.uploadview");
    }

}

?>