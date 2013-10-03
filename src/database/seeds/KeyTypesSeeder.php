<?php namespace Laravella\Crud;

use Laravella\Crud\Log;
use \Seeder;
use \DB;

/**
 * @deprecated see TablesSeeder.php
 */
class SeedKeyTypes extends CrudSeeder {

    public function run()
    {

        DB::table('_db_key_types')->delete();

        $this->addKeyType('primary');
        $this->addKeyType('foreign');
        $this->addKeyType('unique');
    }

}

?>