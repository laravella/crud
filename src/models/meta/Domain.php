<?php class Domain {
    
    //a collection of tables as listed in _db_tables
    private $tables = array();

    /**
     * Create the _db_tables table
     * 
     * @param type $table
     */
    private static function __create__db_tables($tableName)
    {
        Schema::create($tableName, function ($table)
                {
                    $table->increments('id')->unique();
                    $table->string('name', 100)->unique();
                    $table->timestamps();                    
                    
                });
    }

    /**
     * Create the _db_logs table
     * 
     * @param type $table
     */
    private static function __create__db_logs($tableName)
    {
        Schema::create($tableName, function ($table)
                {
                    $table->increments('id')->unique();
                    $table->integer('severity');
                    $table->string('message', 100);
                    $table->timestamps();                    
                    
                });
    }

    /**
     * Create the _db_severities table
     * 
     * @param type $table
     */
    private static function __create__db_severities($tableName)
    {
        Schema::create($tableName, function ($table)
                {
                    $table->increments('id')->unique();
                    $table->string('name', 100)->unique();
                    $table->timestamps();                    
                    
                });
    }

    /**
     * Create the _db_audit table
     * 
     * @param type $table
     */
    private static function __create__db_audit($tableName)
    {
        Schema::create($tableName, function ($table)
                {
                    $table->increments('id')->unique();
                    $table->string('name', 100);
                    $table->timestamps();                    
                    
                });
    }

    
    /**
     * Create the _db_fields table
     * 
     * @param type $table
     */
    private function __create__db_fields($tableName)
    {
        Schema::create($tableName, function ($table)
                {
                    $table->increments('id')->unique();
                    $table->string('name', 100);                        // the field's name
                    $table->string('label', 100);                       // the label
                    $table->integer('display')->nullable();             // the field will be displayed in lists/selects
                    $table->integer('searchable')->nullable();          // 1 if the field is display in a search form, else 0
                    $table->integer('display_order')->nullable();       // the order in which field will be displayed in lists/selects
                    $table->string('type', 100)->nullable();            // datatype
                    $table->integer('length')->nullable();              // datalength
                    $table->integer('width')->nullable();               // display width of the field in pixels
                    $table->string('widget', 250)->nullable();          // json text to define an html widget
                    $table->string('null', 3)->nullable();              // nullable
                    $table->string('key', 50)->nullable();              // type of key
                    $table->string('default', 100)->nullable();         // default value
                    $table->string('extra', 100)->nullable();
                    $table->string('href', 100)->nullable();            //hyperlink this field with the href link
                    $table->integer('_db_table_id')->unsigned();        // links to _db_tables.id
                    $table->integer('pk_field_id')->unsigned();                // links to _db_fields.id (the id of the primary key)
                    $table->integer('pk_display_field_id')->unsigned();        // links to _db_fields.id (the id of a field in the primary table that will be used as a description of the primary key id)
                    $table->timestamps();                    
                    
                    $table->unique(array('_db_table_id', 'name'));
                    $table->foreign('_db_table_id')->references('id')->on('_db_tables')->onDelete('cascade');
                });
    }

    /**
     * Create database table _db_views
     * 
     * @param type $tableName
     */
    private function __create__db_views($tableName)
    {
        Schema::create($tableName, function ($table)
                {
                    $table->increments('id')->unique();
                    $table->string('name', 100);
                    $table->timestamps();                    
                    
                });
    }

    /**
     * Create database table _db_actions
     * 
     * @param type $tableName
     */
    private function __create__db_actions($tableName)
    {
        Schema::create($tableName, function ($table)
                {
                    $table->increments('id')->unique();
                    $table->string('name', 100);
                    $table->timestamps();                    
                    
                });
    }

    /**
     * You can assign a view to a uri so that /db/edit/users can have a different view than /db/edit/accounts
     * 
     * 
     * @param type $tableName
     */
    private function __create__db_table_action_views($tableName)
    {
        Schema::create($tableName, function ($table)
                {
                    $table->increments('id');
                    $table->integer('table_id')->unsigned();
                    $table->integer('action_id')->unsigned();
                    $table->integer('view_id')->unsigned();
                    $table->timestamps();                    

                    $table->foreign('view_id')->references('id')->on('_db_views')->onDelete('cascade');
                    $table->foreign('table_id')->references('id')->on('_db_tables')->onDelete('cascade');
                    $table->foreign('action_id')->references('id')->on('_db_actions')->onDelete('cascade');
                });
    }

    /**
     * Create database table _db_actions
     * 
     * @param type $tableName
     */
    private function __create__db_user_permissions($tableName)
    {
        Schema::create($tableName, function ($table)
                {
                    $table->increments('id')->unique();
                    $table->integer('user_id')->unsigned();
                    $table->integer('table_id')->unsigned();
                    $table->integer('action_id')->unsigned();
                    $table->timestamps();                    
                    
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                    $table->foreign('table_id')->references('id')->on('_db_tables')->onDelete('cascade');
                    $table->foreign('action_id')->references('id')->on('_db_actions')->onDelete('cascade');
                });
    }

    /**
     * Create database table _db_actions
     * 
     * @param type $tableName
     */
    private function __create__db_usergroup_permissions($tableName)
    {
        Schema::create($tableName, function ($table)
                {
                    $table->increments('id')->unique();
                    $table->integer('usergroup_id')->unsigned();
                    $table->integer('table_id')->unsigned();
                    $table->integer('action_id')->unsigned();
                    $table->timestamps();                    
                    
                    $table->foreign('usergroup_id')->references('id')->on('usergroups')->onDelete('cascade');
                    $table->foreign('table_id')->references('id')->on('_db_tables')->onDelete('cascade');
                    $table->foreign('action_id')->references('id')->on('_db_actions')->onDelete('cascade');
                });
    }

    /**
     * Create a database table. To use this function there also needs to be a "__create_".$tableName function in this class.
     * 
     * @param type $tableName
     * @param type $log
     * @throws Exception
     */
    public function create($tableName, &$log)
    {
        if (!Schema::hasTable($tableName))
        {
            try
            {
//$createName is the name of the function which contains the create declarations of the fieldnames
                $createName = "__create_" . $tableName;
                $this->$createName($tableName);
                $log[] = "Table $tableName created";
            }
            catch (Exception $e)
            {
                $log[] = "Table $tableName could not be created.";
                $log[] = $e->getMessage();
                throw new Exception($e);
            }
        }
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
    public function populate(&$log)
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
    public function updateReferences(&$log)
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
    
}

?>
