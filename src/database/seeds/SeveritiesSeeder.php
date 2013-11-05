<?php namespace Laravella\Crud;

use Laravella\Crud\Log;
use \Seeder;
use \DB;

class SeedSeverities extends CrudSeeder {

    public function run()
    {
        DB::table('_db_severities')->delete();
        
        $this->addSeverity("success");
        $this->addSeverity("info");
        $this->addSeverity("warning");
        $this->addSeverity("important");
    }

}
?>