<?php namespace Laravella\Crud;

use Laravella\Crud\Log;
use \Seeder;
use \DB;


class SeedPageTables extends CrudSeeder {
    
    public function run()
    {
        DB::table('_db_page_tables')->delete();
        
    }

}
?>