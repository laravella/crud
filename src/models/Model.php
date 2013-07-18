<?php

/**
 * Description of generic
 *
 * @author Victor
 */
class Model extends Eloquent {
    protected $table = "";
    
    protected $tableMeta = null;

    public function setTable($table) {
        $this->table = $table;
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
                        ->select('_db_fields.name', '_db_tables.name as tableName', '_db_fields.label', '_db_fields.key', 
                                '_db_fields.display', '_db_fields.type', '_db_fields.length', 
                                '_db_fields.default', '_db_fields.extra', '_db_fields.href', 
                                '_db_fields.pk_field_id', '_db_fields.pk_display_field_id', '_db_fields.display_order')
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
     * return an array('table'=>array('name'=>'tablename', 'pk_name'=>'fieldname'), 'fields'=array())
     */
    public static function getTableMeta($tableName) {
        $fieldMeta = Model::getMetaArray($tableName);
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
    public static function getMeta($tableName)
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
     * Get a table's metadata (from _db_fields table) as an array
     * 
     * @param type $tableName
     * @return type
     */
    public static function getMetaArray($tableName)
    {

        //get metadata from database
        $meta = Model::getMeta($tableName);

        $fmeta = Model::getMeta("_db_fields");

        //turn metadata into array
        $ma = Model::makeArray($fmeta, $meta);

        //set field name as key in meta array
        $metaA = array();
        foreach ($ma as $mk)
        {
            //the name of the table
            $mk['tableName'] = $tableName;
            if (!empty($mk['pk_field_id'])) {
                //add primary key's metadata to foreignkey metadata
                $mk['pk'] = Model::getFieldMeta($mk['pk_field_id'], $fmeta);
                if (!empty($mk['pk_display_field_id'])) {
                    //add primary key's (displayed one) metadata to foreignkey metadata
                    $mk['pk_display'] = Model::getFieldMeta($mk['pk_display_field_id'], $fmeta);
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
