<?php  namespace Laravella\Crud;

use Laravella\Crud\Log;
use Seeder;
use \DB;


class CrudRestoreSeeder extends Seeder {


    public function run()
    {
        DB::transaction(function()
        {
        $pdo = DB::connection()->getPdo();
        $this->tableRestore($bakId, $pdo);
        $this->fieldRestore($bakId, $pdo);
        $this->menuRestore($bakId, $pdo);
        $this->permissionsRestore($bakId, $pdo);
        });
    }

    private function tableRestore($bakId, $pdo) {
        $sql = "select t.`name` as `table_name`, tav.page_size, tav.title,
a.name as action_name, v.name as view_name
from _db_table_action_views tav
inner join _db_tables t on tav.table_id = t.id
inner join _db_actions a on tav.action_id = a.id
inner join _db_views v on tav.view_id = v.id";
        
        $tavs = $pdo->query($sql);
        
        foreach($tavs as $tav) {
            echo $tav->table_name."\n";
        }
        
    }
    
    private function fieldRestore($bakId, $pdo) {
        
    }
    
    private function menuRestore($bakId, $pdo) {
        
    }
    
    private function permissionsRestore($bakId, $pdo) {
        
    }
    
    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('id', InputArgument::OPTIONAL, 'List all available backups.'),
            array('list', InputArgument::OPTIONAL, 'List all available backups.'),
            array('restore', InputArgument::OPTIONAL, 'Restore a specific backup, or the latest one if no id is specified.'),
        );
    }
    
    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('id', 'i', InputOption::VALUE_OPTIONAL, 'The id of the backup to restore. Use list argument to list available ids.', null),
        );
    }
    
}
