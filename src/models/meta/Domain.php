<?php class Domain {
    
    //a collection of tables as listed in _db_tables
    private $tables = array();
    
    private $log = array();
    
    /**
     * 
     * @param type $severity
     * @param type $message
     */
    private function __log($severity, $message) {
        $this->log[] = array("severity"=>$severity, "message"=>$message);
    }

    /**
     * Getter for $log
     * 
     * @return type
     */
    public function getLog() {
        return $this->log;
    }
    
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
                    $table->integer('table_id')->unsigned();        // links to _db_tables.id
                    $table->integer('pk_field_id')->unsigned();                // links to _db_fields.id (the id of the primary key)
                    $table->integer('pk_display_field_id')->unsigned();        // links to _db_fields.id (the id of a field in the primary table that will be used as a description of the primary key id)
                    $table->timestamps();                    
                    
                    $table->unique(array('table_id', 'name'));
                    $table->foreign('table_id')->references('id')->on('_db_tables')->onDelete('cascade');
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
                    $table->integer('page_size')->unsigned(); //the size of a page (pagination) in a list view
                    $table->string('title',50);
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
     * @throws Exception
     */
    public function create($tableName)
    {
        if (!Schema::hasTable($tableName))
        {
            try
            {
//$createName is the name of the function which contains the create declarations of the fieldnames
                $createName = "__create_" . $tableName;
                $this->$createName($tableName);
                $this->__log("success", "Table $tableName created");
            }
            catch (Exception $e)
            {
                $this->__log("important", "Table $tableName could not be created.");
                $this->__log("important", $e->getMessage());
                throw new Exception($e);
            }
        }
    }
    
    
    /**
     * populate _db_tables and _db_fields
     * 
     */
    private function __populateMeta()
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
                    $this->__log("success", "Added $tableName to _db_table with id $id");
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
                                // the fields that will go into _db_fields
                                $colRec = array();
                                $colRec['table_id'] = $id;
                                $colRec['name'] = $col->Field;
                                $colRec['label'] = $this->__makeLabel($col->Field);
                                if ($col->Field == "created_at" || $col->Field == "updated_at") {
                                    $colRec['display'] = 0;
                                } else {
                                    $colRec['display'] = 1;
                                }
                                $colRec['searchable'] = 1;
                                $colRec['display_order'] = $displayOrder++;
                                $colRec['type'] = $this->__getFieldType($col->Type);
                                $colRec['length'] = $this->__getFieldLength($col->Type);
                                $colRec['width'] = $this->__getFieldWidth($colRec['type'], $colRec['length']);
                                $colRec['widget'] = $this->__getFieldWidget($colRec['type'], $colRec['length']);
                                $colRec['null'] = $col->Null;
                                $colRec['key'] = $col->Key;
                                $colRec['default'] = $col->Default;
                                $colRec['extra'] = $col->Extra;
                                $fid = DB::table('_db_fields')->insertGetId($colRec);
                                $this->__log("success", " - {$colRec['name']} inserted with id $fid");
                            }
                            catch (Exception $e)
                            {
                                $this->__log("important", $e->getMessage());
                                $message = " x column {$colRec['name']} could not be inserted.";
                                $this->__log("important", $message);
                                throw new Exception($message, 1, $e);
                            }
                        }
                    }
                    catch (Exception $e)
                    {
                        $this->__log("important", $e->getMessage());
                        $message = "Could not select columns for table $tableName";
                        $this->__log("important", $message);
                        throw new Exception($message, 1, $e);
                    }
                }
            }
            catch (Exception $e)
            {
                $this->__log("important", $e->getMessage());
                $message = "Error inserting table name '$tableName' into _db_tables";
                $this->__log("important", $message);
                throw new Exception($message, 1, $e);
            }
        }
    }

    /**
     * 
     * 
     */
    private function __populateViews()
    {
        $arr = array("name" => "crud::dbview");
        $viewId = DB::table('_db_views')->insertGetId($arr);
        $this->__log("success", " - crud::dbview view inserted");
        $this->__populateTableActions($viewId, true);
    }

    /**
     * Populate _db_severities
     * 
     */
    private function __populateSeverities()
    {
        $arr = array("name" => "success");
        $viewId = DB::table('_db_severities')->insertGetId($arr);
        $this->__log("success", " - 'success' severity inserted");
        
        $arr = array("name" => "info");
        $viewId = DB::table('_db_severities')->insertGetId($arr);
        $this->__log("success", " - 'info' severity inserted");
        
        $arr = array("name" => "warning");
        $viewId = DB::table('_db_severities')->insertGetId($arr);
        $this->__log("success", " - 'warning' severity inserted");
        
        $arr = array("name" => "important");
        $viewId = DB::table('_db_severities')->insertGetId($arr);
        $this->__log("success", " - 'error' severity inserted");
        
    }

    /**
     * Populate table _db_table_action_views
     * 
     * @param type $viewId
     * @param type $doPermissions Will also populate permissions tables if true
     * 
     */
    private function __populateTableActions($viewId, $doPermissions = false)
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
                    $arr = array('table_id' => $table->id, 'action_id' => $action->id, 'view_id' => $viewId, 'page_size' => 10, 'title' => $table->name);
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
            $this->__log("success", $e->getMessage());
            $message = "Error inserting record into table.";
            $this->__log("success", $message);
            throw new Exception($message, 1, $e);
        }
    }

    /**
     * 
     * 
     */
    private function __populateActions()
    {
        $arr = array("name" => "getSelect");
        DB::table('_db_actions')->insert($arr);
        $this->__log("success", " - getSelect action created");

        $arr = array("name" => "getInsert");
        DB::table('_db_actions')->insert($arr);
        $this->__log("success", " - getInsert action created");

        $arr = array("name" => "getEdit");
        DB::table('_db_actions')->insert($arr);
        $this->__log("success", " - getEdit action created");

        $arr = array("name" => "postEdit");
        DB::table('_db_actions')->insert($arr);
        $this->__log("success", " - postEdit action created");

        $arr = array("name" => "postDelete");
        DB::table('_db_actions')->insert($arr);
        $this->__log("success", " - postDelete action created");
        
        $arr = array("name" => "getSearch");
        DB::table('_db_actions')->insert($arr);
        $this->__log("success", " - getSearch action created");
    }

    /**
     * Populate all tables
     * 
     * @param type
     */
    public function populate()
    {
        try
        {
            $this->__populateSeverities();
            $this->__log("success", "Populated severities");
            $this->__populateMeta();
            $this->__log("success", "Populated _db_tables and _db_fields");
            $this->__populateActions();
            $this->__log("success", "Populated _db_actions");
            $this->__populateViews();
            $this->__log("success", "Populated _db_views");
        }
        catch (Exception $e)
        {
            $this->__log("success", $e->getMessage());
            $this->__log("success", " x Error populating tables.");
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
    private function __updateReference($fkTableName, $fkFieldName, $pkTableName, $pkFieldName, $pkDisplayFieldName)
    {
        //get the id of the pkTableName in _db_tables
        $fkTableId = DB::table('_db_tables')->where('name', $fkTableName)->pluck('id');

        $pkTableId = DB::table('_db_tables')->where('name', $pkTableName)->pluck('id');

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

        $this->__log("success", "inserting into _db_fields : where 
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
     * 
     * @param type $log
     * @throws Exception
     */
    public function updateReferences()
    {
        try
        {
            // create foreign key references with
            // log, fkTableName, fkFieldName, pkTableName, pkFieldName, pkDisplayFieldName
            
            $this->__updateReference('_db_fields', 'table_id', '_db_tables', 'id', 'name');

            $this->__updateReference('_db_table_action_views', 'view_id', '_db_views', 'id', 'name');
            $this->__updateReference('_db_table_action_views', 'table_id', '_db_tables', 'id', 'name');
            $this->__updateReference('_db_table_action_views', 'action_id', '_db_actions', 'id', 'name');

            $this->__updateReference('_db_user_permissions', 'user_id', 'users', 'id', 'username');
            $this->__updateReference('_db_user_permissions', 'table_id', '_db_tables', 'id', 'name');
            $this->__updateReference('_db_user_permissions', 'action_id', '_db_actions', 'id', 'name');

            $this->__updateReference('_db_usergroup_permissions', 'usergroup_id', 'usergroups', 'id', 'group');
            $this->__updateReference('_db_usergroup_permissions', 'table_id', '_db_tables', 'id', 'name');
            $this->__updateReference('_db_usergroup_permissions', 'action_id', '_db_actions', 'id', 'name');

            $this->__log("success", "Completed foreign key references");
        }
        catch (Exception $e)
        {
            $this->__log("success", "Error while inserting foreign key references.");
            $this->__log("success", $e->getMessage());
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
    private function __getFieldType($fieldType) {
        $start = strpos($fieldType,'(');
        if ($start > 0) {
            $fieldType = substr($fieldType, 0, $start);
            $this->__log("success", "fieldtype : $fieldType");
        }
        return $fieldType;
    }
    
    /**
     * Returns 100 if fieldType = varchar(100) etc.
     */
    private function __getFieldLength($fieldType) {
        $start = strpos($fieldType,'(')+1;
        $len = null;
        if ($start > 0) {
            $count = strpos($fieldType,')')-$start;
            $len = substr($fieldType, $start, $count);
            //$this->__log("success", "fieldtype : $fieldType, start : $start, count : $count, len : $len");
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
