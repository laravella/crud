<?php namespace Laravella\Crud;

use Laravella\Crud\Log;
use \Seeder;
use \DB;


class SeedLogs extends Seeder {

    public function run()
    {

        DB::table('_db_log')->delete();
    }

}
?>