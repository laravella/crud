<?php class Table extends Eloquent {
    protected $tableName = "";
    private $records;
    private $tableMetaData;
    private $dbFields;
    
    public static function get($tableName) {
        $table = new Table();
        $table->tableName = $tableName;
        $table->tableMetaData = Table::getTableMeta($tableName); //Table::getMeta("_db_fields");
        return $table;
    }
    
    /**
     * Gets field metadata from the fieldname and tablename
     */
    public static function getFieldMetaN($fieldName, $tableName) {
        //get metadata of a single field from database
        $fieldMeta = DB::table("_db_fields")
                        ->join('_db_tables', '_db_fields._db_table_id', '=', '_db_tables.id')
                        ->select('_db_fields.name', '_db_tables.name as tableName', 
                                '_db_fields.label', '_db_fields.key', '_db_fields.display', 
                                '_db_fields.type', '_db_fields.length', '_db_fields.default', 
                                '_db_fields.extra', '_db_fields.href', '_db_fields.pk_field_id', 
                                '_db_fields.pk_display_field_id', '_db_fields.display_order', 
                                '_db_fields.width', '_db_fields.widget', '_db_fields.searchable')
                        ->where("_db_tables.name", $tableName)
                        ->where("_db_fields.name", $fieldName)->get();
        
        return Table::__field_meta($fieldMeta);        
    }
    
    /**
     * 
     * 
     * @param type $fieldId the id of the field in _db_fields
     * @param type $dbFieldsMeta [optional] always the result of Table::getMeta("_db_fields");
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
        
        return Table::__field_meta($fieldMeta);

    }

    private static function __field_meta($fieldMeta, $dbFieldsMeta = null) {
        $tableName = $fieldMeta[0]->tableName;

        if (empty($dbFieldsMeta))
        {
            $dbFieldsMeta = Table::getMeta("_db_fields");
        }

        //turn $fieldMeta into an array
        $fieldMetaA = Table::makeArray($dbFieldsMeta, $fieldMeta);
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
        $meta = Table::getMeta($tableName);

        $fmeta = Table::getMeta("_db_fields");

        //turn metadata into array
        $metaA = Table::makeArray($fmeta, $meta);
        
        $fieldMeta = Table::addPkData($tableName, $metaA);
        
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
        $meta = Table::getMeta($tableName);

        $fieldsMeta = Table::getMeta("_db_fields");
        
        //turn metadata into array
        $ma = Table::makeArray($fieldsMeta, $meta);

        $metaA = Table::addPkData($tableName, $ma, $fieldsMeta);
        
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
            $fieldsMeta = Table::getMeta("_db_fields");
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
                $mk['pk'] = Table::getFieldMeta($mk['pk_field_id'], $fieldsMeta);
                if (!empty($mk['pk_display_field_id']))
                {
                    //add primary key's (displayed one) metadata to foreignkey metadata
                    $mk['pk_display'] = Table::getFieldMeta($mk['pk_display_field_id'], $fieldsMeta);
                }
            }
            $metaA[$mk['name']] = $mk;
        }        
        return $metaA;
    }
    
    /**
     * Turn a StdClass object into an array using an array of meta data objects.
     * 
     * @param type $meta An array of stdClass objects, each object representing a field's metadata (_db_fields). 
     *  You can use Table::getMeta($tableName) to get this.
     * @param type $data An array of stdClass objects, each object a record. (the result of DB::table('tableName')->get() not ->first() )
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
    
    /**
     * Turn a StdClass object into an array using an array of meta data arrays.
     * 
     * @param type $meta An array of arrays, each one representing a field's metadata (_db_fields)
     * @param type $data An array of stdClass objects, each object a record
     */
    public static function makeArrayA($metaA, $data)
    {
        $arr = array();
        //loop through records
        foreach ($data as $rec)
        {
            $recA = array();
            //for each fieldname in metadata
            foreach ($metaA as $metaField)
            {
                //get field name
                $fieldName = $metaField['name'];
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
