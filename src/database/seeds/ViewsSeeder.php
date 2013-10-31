<?php namespace Laravella\Crud;

use Laravella\Crud\Log;
use \Seeder;
use \DB;
use \Config;

class SeedViews extends CrudSeeder {

    public function run()
    {
        
        $skin = Config::get('app.skin'); //"flatly";
        
        DB::table('_db_views')->delete();
        
        $this->addView("skins::{$skin}.dbview");
        $this->addView("skins::{$skin}.frontview");
        $this->addView("skins::{$skin}.account.login");

        $this->populateTableActions(true);
        
    }

}
?>