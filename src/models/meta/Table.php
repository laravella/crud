<?php

use Laravella\Crud\DbGopher;

class Table extends Eloquent {

    protected $tableName = "";
    public $tableMetaData = null;
    public $records = array();

    protected $primaryKey;
    private $dbFields;
    private $pageSize = 10;
    private $selectBox = array();

    public static function getMetaFields() {
        return array('_db_fields.id', '_db_fields.name', '_db_tables.name as tableName', '_db_fields.label', 
            '_db_fields.key', '_db_fields.display_type_id', '_db_fields.type', '_db_fields.length', 
            '_db_fields.default', '_db_fields.extra', '_db_fields.href', 
            '_db_key_fields.pk_field_id', '_db_key_fields.pk_display_field_id', 
            '_db_fields.display_order', '_db_fields.width', 
            '_db_fields.widget_type_id', '_db_fields.searchable');
    }
    
    /**
     * Constructor
     * 
     * @param type $tableName
     * @param type $tableMeta
     * @param type $data
     * @param type $pageSize
     * @param type $selectBox
     */
    public function __construct($tableName, $data, $tableMeta = null, $pageSize = 10, $selectBox = array()) {
        $this->records = $data;
        $this->tableName = $tableName;
        if ($tableMeta == null) {
            $this->tableMetaData = Table::getTableMeta($tableName);
        } else {
            $this->tableMetaData = $tableMeta;
        }
        $this->pageSize = $pageSize;
        $this->selectBox = $selectBox;
    }
    
    /**
     * Getter for records
     * 
     * @return type
     */
    public function records() {
        return $this->records;
    }
    
    /**
     * Getter for pageSize
     * 
     * @return type
     */
    public function pageSize() {
        return $this->pageSize;
    }
    
    /**
     * Getter for selectBox
     * 
     * @return type
     */
    public function selectBox() {
        return $this->selectBox();
    }
    
    /*
      'table' => array('name' => $tableName, 'pk_name' => $pkName),
      'fields_array' => $fieldMeta,
      'fields' => $meta,
     */

    public function meta()
    {
        if ($this->tableMetaData == null) {
            $this->tableMetaData = Table::getTableMeta($this->tableName);
        }        
        return $this->$tableMetaData['fields'];
    }

    public function metaA()
    {
        if ($this->tableMetaData == null) {
            $this->tableMetaData = Table::getTableMeta($this->tableName);
        }        
        return $this->$tableMetaData['fields_array'];
    }

    public function pk()
    {
        return $this->$tableMetaData['table']['pk_name'];
    }

    public function name()
    {
        //return $this->tableName;
    }

    protected function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }

    public static function get($tableName)
    {
        $table = new Table();
        $table->setTableName($tableName);
        $table->tableMetaData = Table::getTableMeta($tableName); //Table::getMeta("_db_fields");
        return $table;
    }

    /**
     * Gets field metadata from the fieldname and tablename
     */
    public static function getFieldMetaN($fieldName, $tableName)
    {
        //get metadata of a single field from database
        $fieldMeta = DB::table("_db_fields")
                        ->join('_db_tables', '_db_fields.table_id', '=', '_db_tables.id')
                        ->leftJoin('_db_key_fields', function($join)
                               {
                                   $join->on('_db_key_fields.fk_field_id', '=', '_db_fields.id');
                               })                
                        ->select(static::getMetaFields)
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
                        ->join('_db_tables', '_db_fields.table_id', '=', '_db_tables.id')
                        ->leftJoin('_db_key_fields', function($join)
                               {
                                   $join->on('_db_key_fields.pk_field_id', '=', '_db_fields.id');
                               })                
                        ->select(static::getMetaFields())

                        ->where("_db_fields.id", $fieldId)->get();

        return Table::__field_meta($fieldMeta);
    }

    private static function __field_meta($fieldMeta, $dbFieldsMeta = null)
    {
        $tableName = $fieldMeta[0]->tableName;

        if (empty($dbFieldsMeta))
        {
//            $dbFieldsMeta = Table::getMeta("_db_fields");
            $dbFieldsMeta = Table::getMultiMeta(static::getMetaFields());
        }

        //turn $fieldMeta into an array
        $fieldMetaA = DbGopher::makeArray($dbFieldsMeta, $fieldMeta);
        $fieldMetaA[0]['tableName'] = $tableName;

        return $fieldMetaA[0];
    }

    /**
     * Safely return a value from an array
     * 
     * @param type $arr
     * @param type $key
     * @return type
     */
    public static function val($arr, $key) {
        $val = null;
  //      if (!isset($arr) && !empty($arr) && isset($key) && !empty($key) && is_array($arr) && isset($arr[$key])) {
            $val = $arr[$key];
//        }
        return $val;
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

        $fmeta =  Table::getMultiMeta(static::getMetaFields()); //Table::getMeta("_db_fields");

        //turn metadata into array
        $metaA = DbGopher::makeArray($fmeta, $meta);
        
        $fieldMeta = Table::addPkData($tableName, $metaA);

        $pkName = "";
        foreach ($metaA as $name => $data)
        {
            if (Table::val($data,'key') == 'PRI')
            {
                $pkName = Table::val($data,'name');
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
                        ->join('_db_tables', '_db_fields.table_id', '=', '_db_tables.id')
                        ->join('_db_display_types', '_db_fields.display_type_id', '=', '_db_display_types.id')
                        ->leftJoin('_db_key_fields', function($join)
                               {
                                   $join->on('_db_key_fields.fk_field_id', '=', '_db_fields.id');
                               })  
                        ->select(static::getMetaFields())
                        ->orderBy('display_order', 'asc')
                        ->where("_db_tables.name", "=", $tableName)->get();

        return $tableMeta;
    }
    
    /**
     * get field metadata from database
     * 
     * @param type $tableName
     * @return type
     */
    public static function getMultiMeta($fieldNames)
    {
        
        $tableMeta = DB::table("_db_fields")
                        ->join('_db_tables', '_db_fields.table_id', '=', '_db_tables.id')
                        ->join('_db_display_types', '_db_fields.display_type_id', '=', '_db_display_types.id')
                        ->leftJoin('_db_key_fields', function($join)
                               {
                                   $join->on('_db_key_fields.fk_field_id', '=', '_db_fields.id');
                               })                
                        ->select(static::getMetaFields())
                        ->orderBy('display_order', 'asc')
                        ->whereIn("_db_fields.fullname", $fieldNames)->get();

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

        $fieldsMeta =  Table::getMultiMeta(static::getMetaFields()); //Table::getMeta("_db_fields");

        //turn metadata into array
        $ma = DbGopher::makeArray($fieldsMeta, $meta);

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
    public static function addPkData($tableName, $ma, $fieldsMeta = null)
    {

        //set field name as key in meta array
        $metaA = array();
        
        try {
            if (empty($fieldsMeta))
            {
                $fieldsMeta =  Table::getMultiMeta(static::getMetaFields()); //Table::getMeta("_db_fields");
            }

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
                $fname = Table::val($mk, 'name');
                $metaA[$fname] = $mk;
            }
        } catch(Exception $e) {
            echo $e->getMessage();
        }
        return $metaA;
    }

}

?>
