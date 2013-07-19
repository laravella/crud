<?php

/**
 * Description of generic Model
 *
 * @author Victor
 */
class Model extends Eloquent {

    protected $tableName = "";
    protected $metaData = null;
    
    protected $primaryKey = "id";
    
    protected $guarded = array('id');
    //protected $fillable = array('first_name', 'last_name', 'email');    
    
    
    /**
     * A way to override the constructor. Use this to instantiate the class.
     * 
     * @param type $tableName
     */
    public static function getInstance($tableName)
    {
        $model = new Model();
        $model->setTable($tableName);
        $model->setMetaData($tableName);
        return $model;
    }

    
    public function getA() {
        
    }
    
    /**
     * Check if a field is fillable (updateable)
     * 
     * @param type $fieldName
     * @deprecated since version number
     */
    /*
    private function __isFillable($fieldName) {
        $fillable = false;
        if (isset($this->guarded) && is_array($this->guarded)) {
                if (array_search($fieldName, $this->guarded) || array_search('*', $this->guarded)){
                    $fillable = false;
                } else {
                    $fillable = true;
                }
        } else {
            if (isset($this->fillable) && is_array($this->fillable)) {
                if (array_search($fieldName, $this->fillable)){
                    $fillable = true;
                } else {
                    $fillable = false;
                }
            }
        }
    }
    */
    /**
     * Update a record
     * 
     * @param type $pkValue
     * @return \Model
     */
    public function editRec($pkValue) {
        
        $pkName = $this->metaData['table']['pk_name'];
        
        $fields = $this->metaData['fields_array'];
        
        $updateA = array();
        foreach($fields as $field) 
        {
            //echo $field['name']." : ".$this->isFillable($field['name'])." : ".Input::get($field['name'])."<br />\n";
            
            if ($this->isFillable($field['name'])) {
                $updateA[$field['name']] = Input::get($field['name']);
            }
        }
        //print_r($updateA);
        DB::table($this->tableName)->where($pkName, '=', $pkValue)->update($updateA);
        
        return $this;
    }
    
    /**
     * Setter for table
     * 
     * @param type $tableName
     */
    public function setTable($tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * Setter for metaData
     * 
     * @param type $tableName
     */
    public function setMetaData($tableName)
    {
        $this->metaData = Model::getTableMeta($tableName);
    }

    /**
     * Getter for metaData
     * 
     * @return type
     */
    public function getMetaData() {
        return $this->metaData;
    }
    
    /**
     * 
     * 
     * @param type $fieldId the id of the field in _db_fields
     * @param type $dbFieldsMeta [optional] always the result of Model::getMeta("_db_fields");
     * @return type
     */
    public static function getFieldMeta($fieldId, $dbFieldsMeta = null)
    {
        //get metadata of a single field from database
        $fieldMeta = DB::table("_db_fields")
                        ->join('_db_tables', '_db_fields._db_table_id', '=', '_db_tables.id')
                        ->select('_db_fields.name', '_db_tables.name as tableName', 
                                '_db_fields.label', '_db_fields.key', '_db_fields.display', 
                                '_db_fields.type', '_db_fields.length', '_db_fields.default', 
                                '_db_fields.extra', '_db_fields.href', '_db_fields.pk_field_id', 
                                '_db_fields.pk_display_field_id', '_db_fields.display_order', 
                                '_db_fields.width', '_db_fields.widget', '_db_fields.searchable')
                        ->where("_db_fields.id", $fieldId)->get();

        $tableName = $fieldMeta[0]->tableName;

        if (empty($dbFieldsMeta))
        {
            $dbFieldsMeta = Model::getMeta("_db_fields");
        }

        //turn $fieldMeta into an array
        $fieldMetaA = Model::makeArray($dbFieldsMeta, $fieldMeta);
        $fieldMetaA[0]['tableName'] = $tableName;

        return $fieldMetaA[0];
    }

    /**
     * return an array(
     *  'table'=>array('name'=>'name', 'pk_name'=>'fieldname'), 
     *  'fields_array'=array('fieldname'=>fieldData,...))
     *  
     *  fieldData is in the format : 
     *  
     *  array("id" => 1, "name" => fieldName, "label" => 'Field Name' ...)
     * 
     */
    public static function getTableMeta($tableName)
    {
        
        //get metadata from database
        $meta = Model::getMeta($tableName);

        $fmeta = Model::getMeta("_db_fields");

        //turn metadata into array
        $metaA = Model::makeArray($fmeta, $meta);
        
        $fieldMeta = Model::addPkData($tableName, $metaA);
        
        $pkName = "";
        foreach ($metaA as $name => $data)
        {
            if ($data['key'] == 'PRI')
            {
                $pkName = $data['name'];
            }
        }

        $tmData = array(
            'table' => array('name' => $tableName, 'pk_name' => $pkName), 
            'fields_array' => $fieldMeta,
            'fields' => $meta,
            );
        
        return $tmData;
    }

    /**
     * get field metadata from database
     * 
     * @param type $tableName
     * @return type
     */
    public static function getMeta($tableName)
    {
        $tableMeta = DB::table("_db_fields")
                        ->join('_db_tables', '_db_fields._db_table_id', '=', '_db_tables.id')
                        ->select('_db_fields.name', '_db_fields.label', '_db_fields.key', 
                                '_db_fields.display', '_db_fields.type', '_db_fields.length', 
                                '_db_fields.default', '_db_fields.extra', '_db_fields.href', 
                                '_db_fields.pk_field_id', '_db_fields.pk_display_field_id', 
                                '_db_fields.display_order', '_db_fields.width', '_db_fields.widget', 
                                '_db_fields.searchable')
                        ->orderBy('display_order', 'desc')
                        ->where("_db_tables.name", "=", $tableName)->get();

        return $tableMeta;
    }

    /**
     * Get a table's metadata (from _db_fields table) as an array
     * 
     * @param type $tableName
     * @return type
     */
    public static function getMetaArray($tableName)
    {

        //get metadata from database
        $meta = Model::getMeta($tableName);

        $fieldsMeta = Model::getMeta("_db_fields");
        
        //turn metadata into array
        $ma = Model::makeArray($fieldsMeta, $meta);

        $metaA = Model::addPkData($tableName, $ma, $fieldsMeta);
        
        return $metaA;
    }

    /**
     * 
     * @param type $tableName
     * @param type $ma
     * @param type $mk
     * @param type $fieldsMeta
     * @return type
     */
    public static function addPkData($tableName, $ma, $fieldsMeta = null) {
        
        if (empty($fieldsMeta)) {
            $fieldsMeta = Model::getMeta("_db_fields");
        }

        //set field name as key in meta array
        $metaA = array();
        foreach ($ma as $mk)
        {
            //the name of the table
            $mk['tableName'] = $tableName;
            if (!empty($mk['pk_field_id']))
            {
                //add primary key's metadata to foreignkey metadata
                $mk['pk'] = Model::getFieldMeta($mk['pk_field_id'], $fieldsMeta);
                if (!empty($mk['pk_display_field_id']))
                {
                    //add primary key's (displayed one) metadata to foreignkey metadata
                    $mk['pk_display'] = Model::getFieldMeta($mk['pk_display_field_id'], $fieldsMeta);
                }
            }
            $metaA[$mk['name']] = $mk;
        }        
        return $metaA;
    }
    
    /**
     * Turn a StdClass object into an array.
     * 
     * @param type $meta An array of stdClass objects, each object representing a field's metadata
     * @param type $data An array of stdClass objects, each object a record
     */
    public static function makeArray($meta, $data)
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

}

?>
