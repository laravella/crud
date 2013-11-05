<?php namespace Laravella\Crud;

use Laravella\Crud\Log;
use \Seeder;
use \DB;
use \Config;

class SeedPageTypes extends CrudSeeder {

    public function run()
    {
        
        $skin = Config::get('app.skin');//"flatly";
        
        $this->addPageType("skins::{$skin}.dbview");
        $this->addPageType("skins::{$skin}.frontview");
        $this->addPageType("skins::{$skin}.account.login");
    }

}

?>