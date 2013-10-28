<?php namespace Laravella\Crud;

use Laravella\Crud\Log;
use \Seeder;
use \DB;

class SeedPageTypes extends CrudSeeder {

    public function run()
    {
        $this->addPageType('adminpages');
        $this->addPageType('frontendpages');
    }

}

?>