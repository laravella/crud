<?php class DbController extends Controller {

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

    private static function __getMeta($tableName)
    {
        //get metadata from database
        return DB::table("_db_fields")
                        ->join('_db_tables', '_db_fields._db_table_id', '=', '_db_tables.id')
                        ->select('_db_fields.name', '_db_fields.label', '_db_fields.key', '_db_fields.display', '_db_fields.type', '_db_fields.length', '_db_fields.default', '_db_fields.extra')
                        ->where("_db_tables.name", "=", $tableName)->get();
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

        //get metadata as an array
        $ma = DbController::__getMetaArray($tableName);

        $prefix = array("id" => "/db/edit/$tableName/");
        return View::make("crud::dbview", array('action' => 'select', 'data' => $table, 'prefix' => $prefix, 'meta' => $ma));
    }

    /**
     * Turn a StdClass object into an array.
     * 
     * @param type $meta An array of stdClass objects, each object representing a field's metadata
     * @param type $data An array of stdClass objects, each object a record
     */
    private static function __makeArray($meta, $data)
    {
        $arr = array();
        //loop through records
        foreach ($data as $rec)
        {
            $recA = array();
            //for each fieldname in metadata
            foreach ($meta as $metaField)
            {
                //get field name
                $fieldName = $metaField->name;
                //populate array with value of field
                if (property_exists($rec, $fieldName))
                {
                    $recA[$fieldName] = $rec->$fieldName;
                }
            }
            //add record array to table array
            $arr[] = $recA;
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
    public function getEdit($tableName = null, $id = 0)
    {
        $table = DB::table($tableName)->where('id', '=', $id)->get();
        $prefix = array();

        $meta = DbController::__getMeta($tableName);
        $data = DbController::__makeArray($meta, $table);
        
        //get metadata as an array
        $meta = DbController::__getMetaArray($tableName);
        
        return View::make("crud::dbview", array('action' => 'edit', 'data' => $data, 'meta'=>$meta, 'prefix' => $prefix));
    }

    /**
     * Update data to the database
     * 
     * @param type $tableName
     * @param type $id
     * @return type
     */
    public function postEdit($tableName = null, $id = null) {
        return "saved";
    }
    
    /**
     * Get a table's metadata (from _db_fields table) as an array
     * 
     * @param type $tableName
     * @return type
     */
    private static function __getMetaArray($tableName) {
        
        //get metadata from database
        $meta = DbController::__getMeta($tableName);

        $fmeta = DbController::__getMeta("_db_fields");

        //turn metadata into array
        $ma = DbController::__makeArray($fmeta, $meta);
        
        //set field name as key in meta array
        $metaA = array();
        foreach ($ma as $mk)
        {
            $mk['_db_tables.name'] = $tableName;
            $metaA[$mk['name']] = $mk;
        }
        
        return $metaA;
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
