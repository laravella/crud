<?php class DbInstallController extends Controller {

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
                    $table->integer('_db_table_id');
                    $table->string('name', 100);
                    $table->string('label', 100);
                    $table->integer('display')->nullable();
                    $table->string('type', 100)->nullable();
                    $table->integer('length')->nullable();
                    $table->string('null', 3)->nullable();
                    $table->string('key', 50)->nullable();
                    $table->string('default', 100)->nullable();
                    $table->string('extra', 100)->nullable();
                    $table->string('href', 100)->nullable();            //hyperlink this field with the href link
                    $table->unique(array('_db_table_id', 'name'));
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
                    $table->integer('table_id');
                    $table->integer('action_id');
                    $table->integer('view_id');
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
                    $table->integer('user_id');
                    $table->integer('table_id');
                    $table->integer('action_id');
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
                    $table->integer('usergroup_id');
                    $table->integer('table_id');
                    $table->integer('action_id');
                });
    }
    
    /**
     * Create a database table. To use this function there also needs to be a "__create_".$tableName function in this class.
     * 
     * @param type $tableName
     * @param type $log
     * @throws Exception
     */
    private function __create($tableName, &$log)
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
     * Drop metadata tables and redo an install
     */
    public function getReinstall(&$log = array())
    {
        foreach (DbInstallController::__getAdminTables() as $adminTable) {
            Schema::dropIfExists($adminTable);
            $log[] = "dropped table $adminTable";
        }        
        return $this->getInstall($log);
    }
    
    /**
     * returns an array with a list of tables that are used for admin purposes
     */
    private static function __getAdminTables() {
        return array("_db_tables", 
            "_db_fields", 
            "_db_views",
            "_db_actions",
            "_db_table_action_views",
            "_db_user_permissions",
            "_db_usergroup_permissions"
            );
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
            foreach (DbInstallController::__getAdminTables() as $adminTable) {
                $this->__create($adminTable, $log);
            }
            
            try
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
                                foreach ($cols as $col)
                                {
                                    try
                                    {
                                        $colRec = array();
                                        $colRec['_db_table_id'] = $id;
                                        $colRec['name'] = $col->Field;
                                        $colRec['label'] = $col->Field;
                                        $colRec['display'] = 1;
                                        $colRec['type'] = $col->Type;
                                        $colRec['length'] = 0;
                                        $colRec['null'] = $col->Null;
                                        $colRec['key'] = $col->Key;
                                        $colRec['default'] = $col->Default;
                                        $colRec['extra'] = $col->Extra;
                                        $fid = DB::table('_db_fields')->insertGetId($colRec);
                                        $log[] = " - {$colRec['name']} inserted with id $fid";
                                    }
                                    catch (Exception $e)
                                    {
                                        $log[] = " x column {$colRec['name']} could not be inserted.";
                                        $log[] = $e->getMessage();
                                    }
                                }
                            }
                            catch (Exception $e)
                            {
                                $log[] = "Could not select columns for table $tableName";
                            }
                        }
                    }
                    catch (Exception $e)
                    {
                        $log[] = "Error inserting table name '$tableName' into _db_tables";
                        $log[] = $e->getMessage();
                    }
                }
            }
            catch (Exception $e)
            {
                $log[] = "Error inserting table names into _db_tables";
            }
        }
        catch (Exception $e)
        {
            $log[] = $e->getMessage();
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