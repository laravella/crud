<?php namespace Laravella\Crud;

use Laravella\Crud\Log;
use \Seeder;
use \DB;


class SeedActions extends CrudSeeder {
    
    public function run()
    {

        DB::table('_db_actions')->delete();
        
        $this->addAction("getSelect");
        $this->addAction("getInsert");
        $this->addAction("getEdit");
        $this->addAction("getUpload");
        $this->addAction("postUpload");
        $this->addAction("postEdit");
        $this->addAction("postDelete");
        $this->addAction("getSearch");
        $this->addAction("getPage");
        $this->addAction("getObject");
    }

}
?>