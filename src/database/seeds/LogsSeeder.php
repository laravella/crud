<?php

use Laravella\Crud\Log;

class SeedLogs extends Seeder {

    public function run()
    {

        DB::table('_db_log')->delete();
    }

}
?>