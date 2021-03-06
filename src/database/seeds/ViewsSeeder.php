<?php namespace Laravella\Crud;

use Laravella\Crud\Log;
use \Seeder;
use \DB;
use \Config;

class SeedViews extends CrudSeeder {

    public function run()
    {
        
        $skins = Options::getJsonOptions('skins'); //Config::get('app.skins');
        
        DB::table('_db_views')->delete();
        
        $this->addView("{$skins['admin']}.dbview");
        $this->addView("{$skins['frontend']}.frontview");
        $this->addView("{$skins['admin']}.uploadview");
        $this->addView("{$skins['admin']}.login");

    }

}
?>