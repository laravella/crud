<?php

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
     * 
     * 
     * @param type $fieldId the id of the field in _db_fields
     * @param type $dbFieldsMeta [optional] always the result of DbController::__getMeta("_db_fields");
     * @return type
     */
    private static function __getFieldMeta($fieldId, $dbFieldsMeta = null)
    {
        //get metadata of a single field from database
        $fieldMeta = DB::table("_db_fields")
                        ->join('_db_tables', '_db_fields._db_table_id', '=', '_db_tables.id')
                        ->select('_db_fields.name', '_db_tables.name as tableName', '_db_fields.label', '_db_fields.key', 
                                '_db_fields.display', '_db_fields.type', '_db_fields.length', 
                                '_db_fields.default', '_db_fields.extra', '_db_fields.href', 
                                '_db_fields.pk_field_id', '_db_fields.pk_display_field_id', '_db_fields.display_order')
                        ->where("_db_fields.id", $fieldId)->get();
        
        $tableName = $fieldMeta[0]->tableName;

        if (empty($dbFieldsMeta))
        {
            $dbFieldsMeta = DbController::__getMeta("_db_fields");
        }
        
        //turn $fieldMeta into an array
        $fieldMetaA = DbController::__makeArray($dbFieldsMeta, $fieldMeta);
        $fieldMetaA[0]['tableName'] = $tableName;
        
        return $fieldMetaA[0];
    }    

    /**
     * return an array('table'=>array('name'=>'tablename', 'pk_name'=>'fieldname'), 'fields'=array())
     */
    private static function __getTableMeta($tableName) {
        $fieldMeta = DbController::__getMetaArray($tableName);
        $pkName = "";
        foreach($fieldMeta as $name=>$data) {
            if ($data['key'] == 'PRI') {
                $pkName = $name;
                $pkData = $data;
            }
        }
        
        $tmData = array('table'=>array('name'=>$tableName, 'pk_name'=>$pkName), 'fields'=>$fieldMeta);
        return $tmData;
    }
    
    /**
     * get metadata from database
     * 
     * @param type $tableName
     * @return type
     */
    private static function __getMeta($tableName)
    {
        $tableMeta = DB::table("_db_fields")
                        ->join('_db_tables', '_db_fields._db_table_id', '=', '_db_tables.id')
                        ->select('_db_fields.name', '_db_fields.label', '_db_fields.key', 
                                '_db_fields.display', '_db_fields.type', '_db_fields.length', 
                                '_db_fields.default', '_db_fields.extra', '_db_fields.href', 
                                '_db_fields.pk_field_id', '_db_fields.pk_display_field_id', '_db_fields.display_order')
                        ->orderBy('display_order', 'desc')
                        ->where("_db_tables.name", "=", $tableName)->get();

        return $tableMeta;
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
     * @param type $pkValue
     * @return type
     */
    public function getEdit($tableName = null, $pkValue = 0)
    {
        $tableMeta = DbController::__getTableMeta($tableName);
        
        //get metadata as an array
        $metaA = $tableMeta['fields']; //DbController::__getMetaArray($tableName);
        $meta = DbController::__getMeta($tableName);
        
        //TODO : us primary key instead of id
        $table = DB::table($tableName)->where('id', '=', $pkValue)->get();
        $prefix = array();

        $data = DbController::__makeArray($meta, $table);

        $selects = $this->__getPkSelects($metaA);
        
        return View::make("crud::dbview", 
                array('action' => 'edit', 'data' => $data, 'meta' => $metaA, 
                    'prefix' => $prefix,
                    'selects' => $selects,
                    'tableName' => $tableName));
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
     * Update data to the database
     * 
     * @param type $tableName
     * @param type $id
     * @return type
     */
    public function postEdit($tableName = null, $pkValue = null)
    {
        $pkName = Input::get('meta.pk_name');
        DB::table($tableName)->where($pkName, '=', $pkValue)->update(array());
        return "saved";
    }

    /**
     * Get a table's metadata (from _db_fields table) as an array
     * 
     * @param type $tableName
     * @return type
     */
    private static function __getMetaArray($tableName)
    {

        //get metadata from database
        $meta = DbController::__getMeta($tableName);

        $fmeta = DbController::__getMeta("_db_fields");

        //turn metadata into array
        $ma = DbController::__makeArray($fmeta, $meta);

        //set field name as key in meta array
        $metaA = array();
        foreach ($ma as $mk)
        {
            //the name of the table
            $mk['tableName'] = $tableName;
            if (!empty($mk['pk_field_id'])) {
                //add primary key's metadata to foreignkey metadata
                $mk['pk'] = DbController::__getFieldMeta($mk['pk_field_id'], $fmeta);
                if (!empty($mk['pk_display_field_id'])) {
                    //add primary key's (displayed one) metadata to foreignkey metadata
                    $mk['pk_display'] = DbController::__getFieldMeta($mk['pk_display_field_id'], $fmeta);
                }
            }
            $metaA[$mk['name']] = $mk;
        }

        return $metaA;
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
