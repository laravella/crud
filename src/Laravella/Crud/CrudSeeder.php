<?php namespace Laravella\Crud;

use Laravella\Crud\Log;
use \Seeder;
use \Model;
use \DB;

class CrudSeeder extends Seeder {

    private $idCache = array();

    private $pkTypeId = null;
    private $fkTypeId = null;

    /**
     * Update a reference to primary keys in _db_fields
     * 
     * @param type $fkTableName
     * @param type $fkFieldName
     * @param type $pkTableName
     * @param type $pkFieldName
     */
    public function updateReference($fkTableName, $fkFieldName, $pkTableName, $pkFieldName, $pkDisplayFieldName)
    {
        //get the id of the pkTableName in _db_tables
        $fkTableId = DB::table('_db_tables')->where('name', $fkTableName)->pluck('id');

        $pkTableId = DB::table('_db_tables')->where('name', $pkTableName)->pluck('id');

        if (!isset($this->pkTypeId)) {
            $this->pkTypeId = DB::table('_db_key_types')->where('name', 'primary')->pluck('id');
        }
            
        if (!isset($this->fkTypeId)) {
            $this->fkTypeId = DB::table('_db_key_types')->where('name', 'foreign')->pluck('id');
        }
            
        //get the id of the primary key field in _db_fields
        //for each field in the _db_fields table there will thus be a reference to 
        $pkFieldId = DB::table('_db_fields')
                ->where('table_id', $pkTableId)
                ->where('name', $pkFieldName)
                ->pluck('id');

        $pkDisplayFieldId = DB::table('_db_fields')
                ->where('table_id', $pkTableId)
                ->where('name', $pkDisplayFieldName)
                ->pluck('id');

        $fkFieldId = DB::table('_db_fields')
                ->where('table_id', $fkTableId)
                ->where('name', $fkFieldName)
                ->pluck('id');

        Log::write("success", "inserting into _db_fields : where 
            pkTableName = $pkTableName, 
            pkFieldName = $pkFieldName, 
            pkTableId = $pkTableId, 
            pkFieldId = $pkFieldId, 

            fkTableName = $fkTableName, 
            fkFieldName = $fkFieldName, 
            fkTableId = $fkTableId,
            fkFieldId = $fkFieldId");

//set the reference on the fk field
        DB::table('_db_fields')
                ->where('table_id', $fkTableId)
                ->where('name', $fkFieldName)
                ->update(array('pk_field_id' => $pkFieldId, 'pk_display_field_id' => $pkDisplayFieldId));
        
        DB::table('_db_keys')->insert(array('primary_field_id'=>$pkFieldId, 'foreign_field_id'=>$fkFieldId, 'key_type_id'=>$this->pkTypeId));
        
        /*
          $this->__log("success", "updating record : {$fkRec->id}");

          DB::table('_db_fields')
          ->where('table_id', $fkTableId)
          ->where('name', $fkFieldName)
          ->update(array('pk_field_id' => $fieldId));
         */
    }
    
    /**
     * 
     * @param type $whereField
     * @param type $whereValue
     */
    public function getId($table, $whereField, $whereValue = null)
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
//                throw new DBException(count($recs) . ' Unique id not found where ' . $query);
                return null;
            }
            else
            {
                $idCache[$key] = $recs[0]->id;
            }
        }
        return $idCache[$key];
    }

    /**
     * Add a severity
     * 
     * @param type $severity
     */
    public function addSeverity($severity) {
        $arr = array("name" => $severity);
        $viewId = DB::table('_db_severities')->insertGetId($arr);
        Log::write("success", " - $severity severity inserted");
    }
    
    /**
     * 
     * @param type $optionTypeId
     * @param type $name
     * @param type $value
     * @return type
     */
    public function addOption($optionTypeId, $name, $value)
    {
        $option = array('option_type_id' => $optionTypeId, 'name' => $name, 'value' => $value);
        $optionId = DB::table('_db_options')->insertGetId($option);
        Log::write(Log::INFO, $name . ' option created');
        return $optionId;
    }

    /**
     * Add an option type 
     * 
     * @param type $name
     * @param type $parentId
     * @return type
     */
    public function addOptionType($name, $parentId=null)
    {
        $optionType = array('name' => $name, 'parent_id' => $parentId);
        $optionTypeId = DB::table('_db_option_types')->insertGetId($optionType);
        Log::write(Log::INFO, $name . ' option type created');
        return $optionTypeId;
    }

    /**
     * Add a new usergroup
     * 
     * @param type $groupName
     */
    public function addGroup($groupName) {
        $group = array('name' => $groupName);     //can change permissions
        DB::table('groups')->insert($group);
        Log::write('info', $groupName.' usergroup created');
    }
    
    /**
     * Updates a field or inserts a record if key does not exist
     * 
     * @param type $updateTable
     * @param type $whereValues an array of key value pairs , or an id
     */
    public function delete($updateTable, $whereValues)
    {
        $m = Model::getInstance($updateTable);
        if (is_array($whereValues))
        {
            //$whereField is an array of key-value pairs
            foreach ($whereValues as $key => $value)
            {
                $m = $m->where($key, $value);
            }
        }
        else
        {
            $m = $m->where('id', $whereValues);
        }    
        $m->delete();
    }
    
    /**
     * Updates a field or inserts a record if key does not exist
     * 
     * @param type $updateTable
     * @param type $setField
     * @param type $setValue
     * @param type $whereValues an array of key value pairs , or an id
     * @param type $insertValues Used as a single value if whereField is a string, else excluded
     */
    public function updateOrInsert($updateTable, $whereValues, array $insertValues)
    {
        $m = Model::getInstance($updateTable);
        if (is_array($whereValues))
        {
            //$whereField is an array of key-value pairs
            foreach ($whereValues as $key => $value)
            {
                $m = $m->where($key, $value);
            }
        }
        else
        {
            $m = $m->where('id', $whereValues);
        }
        $recs = $m->distinct()->get();

//$queries = DB::getQueryLog();
//$last_query = end($queries);
//echo var_dump($last_query);
//echo 'last query';

        if (is_object($recs) && count($recs->modelKeys()) > 0)
        {
            //records exist so update
            foreach ($recs as $rec)
            {
                echo 'updating ' . $updateTable . ' ' . $rec->id . ' ' . PHP_EOL;
                $updateM = DB::table($updateTable)->where('id', $rec->id)->update($insertValues);
            }
        }
        else
        {
            echo 'inserting ' . $updateTable . ' ' . PHP_EOL;
            $id = DB::table($updateTable)->insertGetId($insertValues);
            echo $updateTable . ' inserted with id ' . $id . "\n";
        }
    }

    /**
     * Add a menu item
     * 
     * @param type $label
     * @param type $href
     * @param type $iconClass
     * @param type $parentId
     * @return type
     */
    public function addMenu($label, $href, $iconClass = 'icon-file', $parentId = null)
    {
        $group = array('label' => $label, 'href' => $href, 'parent_id' => $parentId, 'icon_class' => $iconClass);
        $menuId = DB::table('_db_menus')->insertGetId($group);
        Log::write('info', $label . ' menu created');
        return $menuId;
    }

    /**
     * Add menu permissions
     * 
     * @param type $menuId
     * @param type $groupName
     */
    public function addMenuPermissions($menuId, $groupName)
    {
        $usergroup = DB::table('usergroups')->where('group', $groupName)->first();
        if (is_object($usergroup))
        {
            $usergroupId = $usergroup->id;
            DB::table('_db_menu_permissions')->insertGetId(array('menu_id' => $menuId, 'usergroup_id' => $usergroupId));
        }
    }

    /**
     * Add widget type
     * 
     * @param type $name
     * @return type
     */
    public function addWidgetType($name)
    {
        $widgetTypeId = DB::table('_db_widget_types')->insertGetId(array('name' => $name));
        Log::write(Log::INFO, $name . ' widget type created');
        return $widgetTypeId;
    }

    /**
     * Add display_type
     * 
     * @param type $id
     * @param type $name
     */
    public function addDisplayType($name, $id = null)
    {
        $displayTypes = array('name' => $name);
        if (!is_null($id)) {
            $displayTypes['id']  = $id; 
        }
        $id = DB::table('_db_display_types')->insertGetId($displayTypes);
        Log::write(Log::INFO, $name . ' display types created');
        return $id;
    }

    /**
     * Add display_type
     * 
     * @param type $id
     * @param type $name
     */
    public function addKeyType($name, $id = null)
    {
        $keyTypes = array('name' => $name);
        if (!is_null($id)) {
            $keyTypes['id']  = $id; 
        }
        $id = DB::table('_db_key_types')->insertGetId($keyTypes);
        Log::write(Log::INFO, $name . ' key type created');
        return $id;
    }

    /**
     * Add action to _db_actions
     * 
     * @param type $actionName
     * @return type
     */
    public function addAction($actionName)
    {
        $arr = array("name" => $actionName);
        $id = DB::table('_db_actions')->insertGetId($arr);
        Log::write("success", " - $actionName action created");
        return $id;
    }

    /**
     * populate _db_views
     * 
     * @param type $viewName
     */
    public function addView($viewName)
    {
        $arr = array("name" => $viewName);
        $viewId = DB::table('_db_views')->insertGetId($arr);
        Log::write("success", " - $viewName view inserted");
    }

    /**
     * Populate table _db_table_action_views
     * 
     * @param type $viewId
     * @param type $doPermissions Will also populate permissions tables if true
     * 
     */
    public function populateTableActions($doPermissions = false)
    {
        try
        {
            DB::table('_db_table_action_views')->delete();
            
            $tables = DB::table('_db_tables')->get();
            $actions = DB::table('_db_actions')->get();
            $views = DB::table('_db_views')->get();

            if ($doPermissions)
            {
                $users = DB::table('users')->get();
                $usergroups = DB::table('usergroups')->get();
            }
            foreach ($views as $view)
            {
                foreach ($tables as $table)
                {
                    foreach ($actions as $action)
                    {
                        $arrTav = array('table_id' => $table->id, 'action_id' => $action->id, 
                            'view_id' => $view->id, 'page_size' => 10, 'title' => $table->name);

                        DB::table('_db_table_action_views')->insert($arrTav);

                        if ($doPermissions)
                        {
                            foreach ($users as $user)
                            {
                                $arr = array('table_id' => $table->id, 'action_id' => $action->id, 'user_id' => $user->id);
                                DB::table('_db_user_permissions')->insert($arr);
                            }
                            foreach ($usergroups as $usergroup)
                            {
                                $arr = array('table_id' => $table->id, 'action_id' => $action->id, 'usergroup_id' => $usergroup->id);
                                DB::table('_db_usergroup_permissions')->insert($arr);
                            }
                        }
                    }
                }
            }
        }
        catch (Exception $e)
        {
            Log::write("success", $e->getMessage());
            $message = "Error inserting record into table.";
            Log::write("success", $message);
            throw new Exception($message, 1, $e);
        }
    }


    /**
     * Replace _ with spaces and make first character of each word uppercase
     * 
     * @param type $name
     */
    public function makeLabel($name)
    {
        return ucwords(str_replace('_', ' ', $name));
}

    /**
     * Returns varchar if fieldType = varchar(100) etc.
     */
    public function getFieldType($fieldType)
    {
        $start = strpos($fieldType, '(');
        if ($start > 0)
        {
            $fieldType = substr($fieldType, 0, $start);
            Log::write("success", "fieldtype : $fieldType");
        }
        return $fieldType;
    }

    /**
     * Returns 100 if fieldType = varchar(100) etc.
     */
    public function getFieldLength($fieldType)
    {
        $start = strpos($fieldType, '(') + 1;
        $len = null;
        if ($start > 0)
        {
            $count = strpos($fieldType, ')') - $start;
            $len = substr($fieldType, $start, $count);
            //$this->__log("success", "fieldtype : $fieldType, start : $start, count : $count, len : $len");
        }

        return $len;
    }

    /**
     * Try and calculate the width of the widget to display the field in 
     */
    public function getFieldWidth($fieldType, $fieldLength)
    {
        return 220;
    }

    /**
     * Try and calculate the best widget to display the field in. Define the widget in json
     */
    public function getFieldWidget($fieldType, $fieldLength)
    {
        return ""; //'{widget" : "input", "attributes" : {"type" : "text"}}';
    }
    
    public function getDisplayType($colRec, $types)
    {

        $displayTypeId = $types['edit'];

        if ($colRec['name'] == "created_at" || $colRec['name'] == "updated_at")
        {
            $displayTypeId = $types['nodisplay'];
        }
        return $displayTypeId;
    }

    
}

?>
