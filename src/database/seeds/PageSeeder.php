<?php namespace Laravella\Crud;

use Laravella\Crud\Log;
use \Seeder;
use \DB;
use \Config;

class SeedPages extends CrudSeeder {

    public function run()
    {
        $this->populateTableActions(true);
    }
}
        
