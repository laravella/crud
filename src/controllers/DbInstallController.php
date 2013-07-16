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
        $this->__populateTableActionViews($log, $viewId);
    }

    /**
     * Populate table _db_table_action_views
     * 
     * @param type $log
     * @param type $viewId
     * 
     */
    private function __populateTableActionViews(&$log, $viewId)
    {
        /*
          insert into _db_table_action_views (view_id, table_id, action_id)
          select v.id view_id, t.id table_id, a.id action_id
          from _db_views v,
          _db_tables t,
          _db_actions a
          where v.id = 1
          ;
         */
    }

    /**
     * 
     * 
     * @param type $log
     */
    private function __populateActions(&$log)
    {
        $arr = array("name" => "select");
        DB::table('_db_actions')->insert($arr);
        $log[] = " - select action created";

        $arr = array("name" => "insert");
        DB::table('_db_actions')->insert($arr);
        $log[] = " - insert action created";

        $arr = array("name" => "update");
        DB::table('_db_actions')->insert($arr);
        $log[] = " - update action created";

        $arr = array("name" => "delete");
        DB::table('_db_actions')->insert($arr);
        $log[] = " - delete action created";
    }

    /**
     * Populate permissions tables
     * 
     */
    private function __populatePermissions(&$log)
    {
        /*
          delete from _db_usergroup_permissions;
          insert into _db_usergroup_permissions (usergroup_id, table_id, action_id)
          select ug.id usergroup_id, t.id table_id, a.id action_id
          from usergroups ug,
          _db_tables t,
          _db_actions a;

          delete from _db_user_permissions;
          insert into _db_user_permissions (user_id, table_id, action_id)
          select u.id user_id, t.id table_id, a.id action_id
          from users u,
          _db_tables t,
          _db_actions a;
         */
    }

    /**
     * Populate all tables
     * 
     * @param type $log
     */
    private function __populate(&$log)
    {
        /*
          "_db_tables",
          "_db_fields",
          "_db_views",
          "_db_actions",
          "_db_table_action_views",
          "_db_user_permissions",
          "_db_usergroup_permissions"
         */

        try
        {
            $this->__populateMeta($log);
            $log[] = "Populated _db_tables and _db_fields";
            $this->__populateActions($log);
            $log[] = "Populated _db_actions";
            $this->__populatePermissions($log);
            $log[] = "Populated _db_user_permissions and _db_usergroup_permissions";
            $this->__populateViews($log);
            $log[] = "Populated _db_views";
        }
        catch (Exception $e)
        {
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
                $this->__create($adminTable, $log);
            }

            try
            {
                $this->__populate($log);
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