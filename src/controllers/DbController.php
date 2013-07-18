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

    /**
     * /db/select/{tablename}
     * 
     * @param type $table
     * @return type
     */
    public function getSelect($tableName = null)
    {
        //select table data from database
        $table = DB::table($tableName)->paginate(5);

        //get metadata as an array
        $ma = Model::getMetaArray($tableName);
        
        $prefix = array("id" => "/db/edit/$tableName/");
        
        return View::make("crud::dbview", array('action' => 'select', 'data' => $table, 'prefix' => $prefix, 'meta' => $ma));
    }

    
    /**
     * Prompt user to insert a new record
     * 
     * @param type $table
     * @param type $pkValue
     * @return type
     */
    public function getInsert($tableName = null)
    {
        $model = Model::getInstance($tableName);
        
        $tableMeta = $model->getMetaData($tableName);
        
        //get metadata as an array
        $metaA = $tableMeta['fields_array']; 
        $meta = $tableMeta['fields']; 
        $pkName = $tableMeta['table']['pk_name'];
        
        $prefix = array();

        //$table = DB::table($tableName)->where($pkName, '=', $pkValue)->get();
        //$data = Model::makeArray($meta, $table);

        $selects = $this->__getPkSelects($metaA);
        
        return View::make("crud::dbview", 
                array('action' => 'insert', 
                    /*'data' => $data[0], */
                    'meta' => $metaA, 
                    'pkName' => $pkName, 
                    'prefix' => $prefix,
                    'selects' => $selects,
                    'tableName' => $tableName));
    }
    
    /**
     * Display a single record on screen to be edited by the user
     * 
     * @param type $table
     * @param type $pkValue
     * @return type
     */
    public function getEdit($tableName = null, $pkValue = 0)
    {
        $model = Model::getInstance($tableName);
        
        $tableMeta = $model->getMetaData($tableName);
        
        //get metadata as an array
        $metaA = $tableMeta['fields_array']; 
        $meta = $tableMeta['fields']; 
        $pkName = $tableMeta['table']['pk_name'];
        
        $table = DB::table($tableName)->where($pkName, '=', $pkValue)->get();
        
        $prefix = array();

        $data = Model::makeArray($meta, $table);

        $selects = $this->__getPkSelects($metaA);
        
        return View::make("crud::dbview", 
                array('action' => 'edit', 
                    'data' => $data[0], 
                    'meta' => $metaA, 
                    'pkName' => $pkName, 
                    'prefix' => $prefix,
                    'selects' => $selects,
                    'tableName' => $tableName));
    }
    
    /**
     * Update data to the database
     * 
     * @param type $tableName
     * @param type $id
     * @return type
     */
    public function postEdit($tableName = null, $pkValue = null)
    {
        
        Model::getInstance($tableName)->editRec($pkValue);
        
        return Redirect::to("/db/edit/$tableName/$pkValue");
    }

    /**
     * Loop through foreign keys and generate an array of select boxes for each
     * related primary key
     * 
     * @param type $meta
     */
    private function __getPkSelects($meta) {
        $selectA = array();
        foreach($meta as $metaField) {
            if(isset($metaField['pk'])) {
                //metadata of the primary key
                $pk = $metaField['pk'];
                //meta data of the field used to display the primary key
                $pkd = $metaField['pk_display'];
                $selectA[$metaField['name']] = $this->__getSelect($pk['tableName'], $pk['name'], $pkd['name']);
            }
        }
        return $selectA;
    }

    /**
     * Get a select array(object(value, text))
     */
    private function __getSelect($table, $valueField, $textField)
    {
        $data = DB::table($table)->select($valueField, $textField)->get();
        $arr = array();
        if (is_array($data))
        {
            foreach($data as $rec)
            {
                $arr[] = array('value'=>$rec->$valueField, 'text'=>$rec->$textField);
            }
        }
        return $arr;
    }

    /**
     * If method is not found
     * 
     * @param type $parameters
     * @return string
     */
    public function missingMethod($parameters)
    {
        print_r($parameters);
        return "missing";
    }

}

?>
