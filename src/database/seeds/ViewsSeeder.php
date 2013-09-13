<?php namespace Laravella\Crud;

use Laravella\Crud\Log;
use \Seeder;
use \DB;

class SeedViews extends CrudSeeder {

    public function run()
    {
        DB::table('_db_views')->delete();
        
        $this->addView("crud::dbview");
        $this->addView("crud::frontview");
    }

}
?>