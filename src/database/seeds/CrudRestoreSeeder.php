<?php

namespace Laravella\Crud;

use DB;
use Laravella\Crud\Exceptions\DBException;
use Seeder;
use \Model;

class CrudRestoreSeeder extends Seeder {

    private $backupId = null;
    private $pdo = null;

    public function run($bakId = null)
    {
        $this->pdo = DB::connection()->getPdo();
        $this->backupId = empty($bakId) ? $this->__getMaxId() : $bakId;

        echo "Restoring backup : " . $this->backupId . "\n";

        DB::transaction(function()
                {
                    $this->bakId = empty($this->backupId) ? $this->__getMaxId() : $this->backupId;
                    $this->tableRestore();
                    $this->fieldRestore();
                    $this->menuRestore();
                    $this->permissionsRestore();
                });
    }

    private function __getMaxId()
    {
        $sql = "SELECT max(id) as backup_id FROM _db_backups";
        $bu = DB::select($sql);
        $bid = (is_array($bu)) ? $bu[0]->backup_id : null;
        return $bid;
    }

    /**
     * Updates a field or inserts a record if key does not exist
     * 
     * @param type $updateTable
     * @param type $setField
     * @param type $setValue
     * @param type $whereField Could be a field name or an array of key value pairs , in which case whereValue would be excluded
     * @param type $whereValue Used as a single value if whereField is a string, else excluded
     */
    private function __updateOrInsert($updateTable, $setField, $setValue, $whereField, $whereValue = null)
    {
        $m = Model::getInstance(array(), $updateTable);
        if (is_array($whereField))
        {
            //$whereField is an array of key-value pairs
            foreach ($whereField as $key => $value)
            {
                $m->where($key, $value);
            }
        }
        else
        {
            $m->where($whereField, $whereValue);
        }
        $recs = $m->get();
        if (is_object($recs))
        {
            //records exist so update
            foreach ($recs as $rec)
            {
                echo 'updating '.$updateTable . ' ' . $rec->id . ' ' . $setField .'='. $setValue . PHP_EOL;
            }
        }
        else
        {
            echo 'inserting '.$updateTable . ' ' . $rec->id . ' ' . $setField .'='. $setValue . PHP_EOL;
        }
    }

    private $idCache = array();

    /**
     * 
     * @param type $whereField
     * @param type $whereValue
     */
    private function getId($table, $whereField, $whereValue = null)
    {
        $key = $table . ':' . $whereField . ':' . $whereValue;
        if (!isset($this->idCache[$key]))
        {
            $m = Model::getInstance(array(), $table);
            $query = $table . ' ';
            if (is_array($whereField))
            {
                //$whereField is an array of key-value pairs
                foreach ($whereField as $key => $value)
                {
                    $m = $m->where($key, $value);
                    $query .= $key . '=\'' . $value . '\' ';
                }
            }
            else
            {
                $m = $m->where($whereField, $whereValue);
                $query .= $whereField . '=\'' . $whereValue . '\' ';
            }
            $recs = $m->get();
            if (count($recs) != 1)
            {
                throw new DBException(count($recs) . ' Unique id not found where ' . $query);
            }
            else
            {
                $idCache[$key] = $recs[0]->id;
            }
        }
        return $idCache[$key];
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
    private function tableRestore()
    {
        $tavs = Model::getInstance('_db_bak_tables')->where('backup_id', $this->backupId)->get()->toArray();

        foreach ($tavs as $tav)
        {
            try
            {
                $tableId = $this->getId('_db_tables', 'name', $tav['table_name']);
                $actionId = $this->getId('_db_actions', 'name', $tav['action_name']);
                $viewId = $this->getId('_db_views', 'name', $tav['view_name']);

                $this->__updateOrInsert('_db_table_action_views', 'page_size', $tav['page_size'], 
                        array('table_id'=>$tableId, 'action_id'=>$actionId, 'view_id'=>$viewId));

            }
            catch (DBException $e)
            {
                echo $e->getMessage() . "\n";
            }
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
    private function fieldRestore()
    {
        $sql = "select * from _db_bak_fields where backup_id = {$this->backupId}";

        $fields = DB::select($sql);

        foreach ($fields as $field)
        {
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
    private function menuRestore()
    {
        
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
    private function permissionsRestore()
    {
        
    }

}
