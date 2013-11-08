<?php namespace Laravella\Crud;

use Laravella\Crud\Log;
use \Seeder;
use \DB;
use \Config;

class SeedPageTypes extends CrudSeeder {

    public function run()
    {
        
        $skin = Config::get('app.skin');//"flatly";
        
        $this->addPageType("{$skin}.dbview");
        $this->addPageType("{$skin}.frontview");
        $this->addPageType("{$skin}.account.login");
    }

}

?>