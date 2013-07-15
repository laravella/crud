<?php

/**
 * Description of DbController
 *
 * @author Victor
 */
class DbController extends Controller {

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
     * List the tables in the database with links to edit the contents
     * 
     * @return type
     */
    public function getTables()
    {
        $results = DB::select('show tables');
        $prefix = "/db/select/";
        return View::make("crud::dbview", array('action' => 'select', 'data' => $results, 'prefix' => $prefix));
    }

    /**
     * /db/select/{tablename}
     * 
     * @param type $table
     * @return type
     */
    public function getSelect($tableName = null)
    {
        //select table data from database
        $table = DB::table($tableName)->get();
        
        //get metadata from database
        $meta = DB::table("_db_fields")
                ->join('_db_tables', '_db_fields._db_table_id', '=', '_db_tables.id')
                ->select('_db_fields.name', '_db_fields.label', '_db_fields.key', 
                        '_db_fields.display', '_db_fields.type', '_db_fields.length', 
                        '_db_fields.default', '_db_fields.extra')
                ->where("_db_tables.name", "=", $tableName)->get();
        
        //set field name as key in meta array
        $ma = array();
        foreach($meta as $mk) {
            $ma[$mk->name] = $mk;
        }
        
        $prefix = "/db/edit/$tableName/";
        return View::make("crud::dbview", array('action' => 'select', 'data' => $table, 'prefix' => $prefix, 'meta' => $ma));
    }

    /**
     * Turn a StdClass object into an array
     * 
     * @param type $meta
     * @param type $data
     */
    private function __makeArray($tableName, $meta, $data) {
        $arr = array();
        foreach($data as $rec) {
            $rec = array();
            foreach ($meta as $metaField) {
                $rec[$metaField->name] = $rec->$metaField;
            }
            $arr[] = $rec;
        }
        return $arr;
    }
    
    /**
     * Display a single record on screen to be edited by the user
     * 
     * @param type $table
     * @param type $id
     * @return type
     */
    public function getEdit($table = null, $id = 0) {
        $table = DB::table($table)->where('id', '=', $id)->get();
        return View::make("crud::dbview", array('action' => 'edit', 'data' => $table, 'prefix' => ''));
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
                    $table->unique(array('_db_table_id', 'name'));
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
     * Generate metadata from the database and insert it into _db_tables
     * 
     * @param type $table
     * @return type
     */
    public function getInstall()
    {
        try
        {
            $log = array();
            $this->__create('_db_tables', $log);
            $this->__create('_db_fields', $log);
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
