<?php

class DbInstallController extends Controller {

    protected $layout = 'crud::layouts.default';

    /**
     * The root of the crud application /db
     * 
     * @return type
     */
    public function getIndex()
    {
        return View::make("crud::dbinstall", array('action' => 'index'));
    }

    /**
     * Update a reference to primary keys in _db_fields
     * 
     * @param type $fkTableName
     * @param type $fkFieldName
     * @param type $pkTableName
     * @param type $pkFieldName
     */
    private function __updateReference(&$log, $fkTableName, $fkFieldName, $pkTableName, $pkFieldName, $pkDisplayFieldName)
    {
        //get the id of the pkTableName in _db_tables
        $fkTableId = DB::table('_db_tables')->where('name', $fkTableName)->pluck('id');

        $pkTableId = DB::table('_db_tables')->where('name', $pkTableName)->pluck('id');

        //get the id of the primary key field in _db_fields
        //for each field in the _db_fields table there will thus be a reference to 
        $pkFieldId = DB::table('_db_fields')
                ->where('_db_table_id', $pkTableId)
                ->where('name', $pkFieldName)
                ->pluck('id');

        $pkDisplayFieldId = DB::table('_db_fields')
                ->where('_db_table_id', $pkTableId)
                ->where('name', $pkDisplayFieldName)
                ->pluck('id');

        $fkFieldId = DB::table('_db_fields')
                ->where('_db_table_id', $fkTableId)
                ->where('name', $fkFieldName)
                ->pluck('id');

        $log[] = "inserting into _db_fields : where 
            pkTableName = $pkTableName, 
            pkFieldName = $pkFieldName, 
            pkTableId = $pkTableId, 
            pkFieldId = $pkFieldId, 

            fkTableName = $fkTableName, 
            fkFieldName = $fkFieldName, 
            fkTableId = $fkTableId,
            fkFieldId = $fkFieldId";

//set the reference on the fk field
        DB::table('_db_fields')
                ->where('_db_table_id', $fkTableId)
                ->where('name', $fkFieldName)
                ->update(array('pk_field_id' => $pkFieldId, 'pk_display_field_id' => $pkDisplayFieldId));
        /*
          $log[] = "updating record : {$fkRec->id}";

          DB::table('_db_fields')
          ->where('_db_table_id', $fkTableId)
          ->where('name', $fkFieldName)
          ->update(array('pk_field_id' => $fieldId));
         */
    }

    /**
     * 
     * 
     * @param type $log
     * @throws Exception
     */
    private function __updateReferences(&$log)
    {
        try
        {
            // create foreign key references with
            // log, fkTableName, fkFieldName, pkTableName, pkFieldName, pkDisplayFieldName
            
            $this->__updateReference($log, '_db_fields', '_db_table_id', '_db_tables', 'id', 'name');

            $this->__updateReference($log, '_db_table_action_views', 'view_id', '_db_views', 'id', 'name');
            $this->__updateReference($log, '_db_table_action_views', 'table_id', '_db_tables', 'id', 'name');
            $this->__updateReference($log, '_db_table_action_views', 'action_id', '_db_actions', 'id', 'name');

            $this->__updateReference($log, '_db_user_permissions', 'user_id', 'users', 'id', 'username');
            $this->__updateReference($log, '_db_user_permissions', 'table_id', '_db_tables', 'id', 'name');
            $this->__updateReference($log, '_db_user_permissions', 'action_id', '_db_actions', 'id', 'name');

            $this->__updateReference($log, '_db_usergroup_permissions', 'usergroup_id', 'usergroups', 'id', 'group');
            $this->__updateReference($log, '_db_usergroup_permissions', 'table_id', '_db_tables', 'id', 'name');
            $this->__updateReference($log, '_db_usergroup_permissions', 'action_id', '_db_actions', 'id', 'name');

            $log[] = "Completed foreign key references";
        }
        catch (Exception $e)
        {
            $log[] = "Error while inserting foreign key references.";
            $log[] = $e->getMessage();
            throw new Exception($e);
        }
    }

    /**
     * Drop metadata tables and redo an install
     */
    public function getReinstall(&$log = array())
    {
        foreach (DbInstallController::__getAdminTables(true) as $adminTable)
        {
            Schema::dropIfExists($adminTable);
            $log[] = "dropped table $adminTable";
        }
        return $this->getInstall($log);
    }

    /**
     * returns an array with a list of tables that are used for admin purposes
     * 
     * @param type $dropSafe Set dropSafe = true if tables should be returned in an order that is safe to drop them 
     * @return type String[]
     */
    private static function __getAdminTables($dropSafe = false)
    {
        if (!$dropSafe)
        {
            return array("_db_tables",
                "_db_fields",
                "_db_views",
                "_db_actions",
                "_db_table_action_views",
                "_db_user_permissions",
                "_db_usergroup_permissions");
        }
        else
        {
            return array("_db_table_action_views",
                "_db_user_permissions",
                "_db_usergroup_permissions",
                "_db_fields",
                "_db_views",
                "_db_actions",
                "_db_tables");
        }
    }

    /**
     * Replace _ with spaces and make first character of each word uppercase
     * 
     * @param type $name
     */
    private function __makeLabel($name)
    {
        return ucwords(str_replace('_', ' ', $name));
    }

    /**
     * Returns varchar if fieldType = varchar(100) etc.
     */
    private function __getFieldType($fieldType, &$log) {
        $start = strpos($fieldType,'(');
        if ($start > 0) {
            $fieldType = substr($fieldType, 0, $start);
            $log[] = "fieldtype : $fieldType";
        }
        return $fieldType;
    }
    
    /**
     * Returns 100 if fieldType = varchar(100) etc.
     */
    private function __getFieldLength($fieldType, &$log) {
        $start = strpos($fieldType,'(')+1;
        $len = null;
        if ($start > 0) {
            $count = strpos($fieldType,')')-$start;
            $len = substr($fieldType, $start, $count);
            //$log[] = "fieldtype : $fieldType, start : $start, count : $count, len : $len";
        }

        return $len;
    }
    
    /**
     * Try and calculate the width of the widget to display the field in 
     */
    private function __getFieldWidth($fieldType, $fieldLength) {
        return 100;
    }    
    
    /**
     * Try and calculate the best widget to display the field in. Define the widget in json
     */
    private function __getFieldWidget($fieldType, $fieldLength) {
        return ""; //'{widget" : "input", "attributes" : {"type" : "text"}}';
    }    
    
    /**
     * populate _db_tables and _db_fields
     * 
     */
    private function __populateMeta(&$log)
    {
//get the list of tables from the database metadata
        $tables = DB::select('show tables');
//loop through records, each record has a tablename
        foreach ($tables as $table)
        {
            try
            {
//there is only one field, get it
                foreach ($table as $tableName)
                {
//insert it into _db_tables
                    $id = DB::table('_db_tables')->insertGetId(array('name' => $tableName));
                    $log[] = "Added $tableName to _db_table with id $id";
                    try
                    {
//get columns from database
                        $cols = DB::select("show columns from $tableName");
//loop through list of columns
                        $displayOrder = 0;
                        foreach ($cols as $col)
                        {
                            try
                            {
                                $colRec = array();
                                $colRec['_db_table_id'] = $id;
                                $colRec['name'] = $col->Field;
                                $colRec['label'] = $this->__makeLabel($col->Field);
                                $colRec['display'] = 1;
                                $colRec['searchable'] = 1;
                                $colRec['display_order'] = $displayOrder++;
                                $colRec['type'] = $this->__getFieldType($col->Type, $log);
                                $colRec['length'] = $this->__getFieldLength($col->Type, $log);
                                $colRec['width'] = $this->__getFieldWidth($colRec['type'], $colRec['length']);
                                $colRec['widget'] = $this->__getFieldWidget($colRec['type'], $colRec['length']);
                                $colRec['null'] = $col->Null;
                                $colRec['key'] = $col->Key;
                                $colRec['default'] = $col->Default;
                                $colRec['extra'] = $col->Extra;
                                $fid = DB::table('_db_fields')->insertGetId($colRec);
                                $log[] = " - {$colRec['name']} inserted with id $fid";
                            }
                            catch (Exception $e)
                            {
                                $log[] = $e->getMessage();
                                $message = " x column {$colRec['name']} could not be inserted.";
                                $log[] = $message;
                                throw new Exception($message, 1, $e);
                            }
                        }
                    }
                    catch (Exception $e)
                    {
                        $log[] = $e->getMessage();
                        $message = "Could not select columns for table $tableName";
                        $log[] = $message;
                        throw new Exception($message, 1, $e);
                    }
                }
            }
            catch (Exception $e)
            {
                $log[] = $e->getMessage();
                $message = "Error inserting table name '$tableName' into _db_tables";
                $log[] = $message;
                throw new Exception($message, 1, $e);
            }
        }
    }

    /**
     * 
     * 
     * @param type $log
     */
    private function __populateViews(&$log)
    {
        $arr = array("name" => "crud::dbview");
        $viewId = DB::table('_db_views')->insertGetId($arr);
        $log[] = " - crud::dbview view inserted";
        $this->__populateTableActions($log, $viewId, true);
    }

    /**
     * Populate table _db_table_action_views
     * 
     * @param type $log
     * @param type $viewId
     * @param type $doPermissions Will also populate permissions tables if true
     * 
     */
    private function __populateTableActions(&$log, $viewId, $doPermissions = false)
    {
        try
        {
            $tables = DB::table('_db_tables')->get();
            $actions = DB::table('_db_actions')->get();

            if ($doPermissions)
            {
                $users = DB::table('users')->get();
                $usergroups = DB::table('usergroups')->get();
            }
            foreach ($tables as $table)
            {
                foreach ($actions as $action)
                {
                    $arr = array('table_id' => $table->id, 'action_id' => $action->id, 'view_id' => $viewId);
                    DB::table('_db_table_action_views')->insert($arr);
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
        catch (Exception $e)
        {
            $log[] = $e->getMessage();
            $message = "Error inserting record into table.";
            $log[] = $message;
            throw new Exception($message, 1, $e);
        }
    }

    /**
     * 
     * 
     * @param type $log
     */
    private function __populateActions(&$log)
    {
        $arr = array("name" => "getSelect");
        DB::table('_db_actions')->insert($arr);
        $log[] = " - getSelect action created";

        $arr = array("name" => "getInsert");
        DB::table('_db_actions')->insert($arr);
        $log[] = " - getInsert action created";

        $arr = array("name" => "getEdit");
        DB::table('_db_actions')->insert($arr);
        $log[] = " - getEdit action created";

        $arr = array("name" => "postEdit");
        DB::table('_db_actions')->insert($arr);
        $log[] = " - postEdit action created";

        $arr = array("name" => "postDelete");
        DB::table('_db_actions')->insert($arr);
        $log[] = " - postDelete action created";
        
        $arr = array("name" => "getSearch");
        DB::table('_db_actions')->insert($arr);
        $log[] = " - getSearch action created";
    }

    /**
     * Populate all tables
     * 
     * @param type $log
     */
    private function __populate(&$log)
    {
        try
        {
            $this->__populateMeta($log);
            $log[] = "Populated _db_tables and _db_fields";
            $this->__populateActions($log);
            $log[] = "Populated _db_actions";
            $this->__populateViews($log);
            $log[] = "Populated _db_views";
        }
        catch (Exception $e)
        {
            $log[] = $e->getMessage();
            $log[] = " x Error populating tables.";
            throw new Exception("Error populating tables.", 1, $e);
        }
    }

    /**
     * Generate metadata from the database and insert it into _db_tables
     * 
     * @param type $table
     * @return type
     */
    public function getInstall(&$log = array())
    {
        try
        {
//create all the tables
            foreach (DbInstallController::__getAdminTables() as $adminTable)
            {
                $domain = new Domain();
                $domain->create($adminTable, $log);
            }

            try
            {
                $this->__populate($log);
                $this->__updateReferences($log);
            }
            catch (Exception $e)
            {
                $log[] = $e->getMessage();
                $message = " x Error populating tables.";
                $log[] = $message;
                throw new Exception($message, 1, $e);
            }
            $log[] = "Installation completed successfully.";
        }
        catch (Exception $e)
        {
            $log[] = $e->getMessage();
            $message = " x Error during installation.";
            $log[] = $message;
//throw new Exception($message, 1, $e);
        }
        return View::make("crud::dbinstall", array('action' => 'install', 'log' => $log));
    }

    /**
     * If method is not found
     * 
     * @param type $parameters
     * @return string
     */
    public function missingMethod($parameters)
    {
        return "missing";
    }

}

?>