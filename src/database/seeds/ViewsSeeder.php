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
        
        $this->addView("{$skin}.dbview");
        $this->addView("{$skin}.frontview");
        $this->addView("{$skin}.account.login");

    }

}
?>