<?php namespace Laravella\Crud;

use Laravella\Crud\Log;
use \Seeder;
use \DB;

class SeedViews extends CrudSeeder {

    public function run()
    {
        
        $skin = "flatly";
        
        DB::table('_db_views')->delete();
        
        $this->addView("skins::$skin.dbview");
        $this->addView("skins::$skin.frontview");
        
        $this->populateTableActions(true);
        
    }

}
?>