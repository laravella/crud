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
    
    /**
Tables:
backup_id, 
table_name, 
page_size, 
tav_title, 
action_name, 
view_name
     * 
     */
    private function tableRestore() {
        $sql = "select * from _db_bak_tables where backup_id = {$this->backupId}";
        
        $tavs =  DB::select($sql);
        
        foreach($tavs as $tav) {
            echo DBGopher::pick($tav, 'table_name').' '.$tav->action_name.' '.$tav->view_name."\n";
            DB::table('_db_tables')->where('name', $tav->table_name)->first();
        }
        
    }
    
    /**

Fields:
backup_id, 
table_id, 
table_name,
field_id, 
field_name, 
f.`fullname`, 
f.`label`, 
f.`display_type_id`,
f.`searchable`, 
f.`display_order`, 
field_type, 
f.`length`, 
f.`width`, 
f.`null` nullable, 
f.`key`, 
f.`default`, 
f.extra, 
f.href, 
f.pk_field_id, 
pf.`name` as pk_name,
f.pk_display_field_id, 
df.`name` as pk_display_name,
ft.id as pk_table_id, 
ft.`name` as pk_table_name,
f.`widget_type_id`, 
wt.`name` widget_type_name, 
wt.`definition` widget_type_definition,
dt.`name` display_type_name
	     * 
     */
    private function fieldRestore() {
        $sql = "select * from _db_bak_fields where backup_id = {$this->backupId}";
        
        $fields =  DB::select($sql);
        
        foreach($fields as $field) {
            //echo $field['table_name'].' '.$tav['action_name'].' '.$tav['view_name']."\n";
        }
    }
    
/**
	
 _db_bak_menus :
 backup_id, 
 m.id, 
 m.icon_class, 
 m.label, 
 m.href, 
 m.parent_id,
 ug.group as group_name
  * 
 */    
    private function menuRestore() {
        
    }
/**
permissions : 
backup_id, 
u.username, 
u.email, 
u.`password`,
u.first_name, 
u.last_name, 
api_token, 
usergroup_id, 
ug.`group`,
deleted_at, 
t.`name` `table_name`, 
a.`name` action_name 
 */    
    private function permissionsRestore() {
        
    }
    
}
