<?php  namespace Laravella\Crud;

use Laravella\Crud\Log;
use Seeder;
use \DB;


class CrudRestoreSeeder extends Seeder {

    private $backupId = null;
    private $pdo = null;

    public function run($bakId=null)
    {
        $this->pdo = DB::connection()->getPdo();
        $this->backupId = empty($bakId)?$this->getMaxId():$bakId;
        
        echo "Restoring backup : ".$this->backupId."\n";
        
        DB::transaction(function()
        {
        $this->bakId = empty($this->backupId)?$this->getMaxId():$this->backupId;
        $this->tableRestore();
        $this->fieldRestore();
        $this->menuRestore();
        $this->permissionsRestore();
        });
    }

    private function getMaxId() {
        $sql = "SELECT max(id) as backup_id FROM _db_backups";
        $bu = DB::select($sql);
        $bid = (is_array($bu))?$bu[0]->backup_id:null;
        return $bid;
    }
    
    private function tableRestore() {
        $sql = "select * from _db_bak_tables where backup_id = {$this->backupId}";
        
        $tavs = $this->pdo->query($sql);
        
        foreach($tavs as $tav) {
            echo $tav['table_name'].' '.$tav['action_name'].' '.$tav['view_name']."\n";
        }
        
    }
    
    private function fieldRestore() {
        
    }
    
    private function menuRestore() {
        
    }
    
    private function permissionsRestore() {
        
    }
    
}
