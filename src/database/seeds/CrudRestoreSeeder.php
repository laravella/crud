<?php  namespace Laravella\Crud;

use Laravella\Crud\Log;
use \Seeder;
use \DB;


class CrudRestoreSeeder extends Seeder {

    public function run()
    {
        if (Schema::hasTable('_db_backups'))
        {
        }

        if (Schema::hasTable('_db_bak_menus'))
        {
        }

        if (Schema::hasTable('_db_bak_permissions'))
        {
        }
    }

}
