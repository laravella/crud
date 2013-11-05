<?php

/**
 * Description of generic Model
 *
 * @author Victor
 */
class Model extends Eloquent {

    protected $metaData = null;
    protected $primaryKey = "id";
    protected $guarded = array('id');

    /**
     * A way to overload the constructor. Use this to instantiate the class.
     * 
     * @param type $tableName
     */
    public static function getInstance()
    {
        $a = func_get_args(); 
        $i = func_num_args();
        
        $tmpInstance = new static;
        
        if (method_exists($tmpInstance, $f='getInstance'.$i)) { 
            return call_user_func_array(array($tmpInstance,$f),$a); 
        } 
    }

    /**
     * A way to overload the constructor. Use this to instantiate the class.
     * 
     * @param type $tableName
     */
    private static function getInstance1($tableName = null)
    {
        $model = new Model(array(), $tableName);
        return $model;
    }

    /**
     * A way to overload the constructor. Use this to instantiate the class.
     * 
     * @param type $tableName
     */
    private static function getInstance2($attributes, $tableName = null)
    {
        $model = new Model($attributes, $tableName);
        return $model;
    }

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @param  tableName  $tableName
     * 
     * @return void
     */
    function __construct() 
    { 
        $a = func_get_args(); 
        $i = func_num_args(); 
        if (method_exists($this,$f='__construct'.$i)) { 
            call_user_func_array(array($this,$f),$a); 
        } 
    } 

    private function __construct1(array $attributes = array()) 
    { 
        parent::__construct($attributes);
    } 
    
    private function __construct2(array $attributes = array(), $table = null) 
    { 
        $this->table = $table;
        $this->setTable($table);
        $this->setMetaData($table);
        $this->setGuarded(array($this->metaData['table']['pk_name']));
        parent::__construct($attributes);
    } 
        
    
    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable()
    {
        if (isset($this->table))
            return $this->table;

        return null;
    }

    /**
     * 
     * @param type $table
     * @param type $customKey
     * @return type
     */
    public function setHasOne($table, $customKey)
    {
        return $this->hasOne($table, $customKey);
    }

    /**
     * 
     * @param type $table
     * @param type $customKey
     * @return type
     */
    public function setHasMany($table, $customKey)
    {
        return $this->hasMany($table, $customKey);
    }

    /**
     * 
     * @param type $table
     * @param type $customKey
     * @return type
     */
    public function setBelongsTo($table, $customKey)
    {
        return $this->belongsTo($table, $customKey);
    }

    /**
     * 
     * @param type $table
     * @param type $pivotTable
     * @param type $remoteId
     * @param type $localId
     * @return type
     */
    public function setBelongsToMany($table, $pivotTable, $remoteId, $localId)
    {
        return $this->belongsToMany($table, $pivotTable, $remoteId, $localId);
    }

    public function setGuarded($guardedA)
    {
        $this->guarded = $guardedA;
    }

    public function getA()
    {
        
    }

    /**
     * Insert a new record
     */
    public function insertRec()
    {
        $fields = $this->metaData['fields_array'];

        $updateA = array();
        foreach ($fields as $field)
        {
            if ($this->isFillable($field['name']))
            {
                $updateA[$field['name']] = Input::get($field['name']);
            }
        }

        $id = DB::table($this->table)->insertGetId($updateA);

        return $id;
    }

    /**
     * Create an array of key values from http GET data
     * 
     * @param type $fields
     */
    private function __editGet($fields)
    {
        $updateA = array();
        foreach ($fields as $field)
        {
            if ($this->isFillable($field['name']))
            {
                $updateA[$field['name']] = Input::get($field['name']);
            }
        }
        return $updateA;
    }

    /**
     * Create an array of key values from http GET data
     * 
     * @param type $fields
     * @param type $json
     */
    private function __editJson($fields, $json)
    {
        die;

        $updateA = array();
        //print_r($json);
        $json = iconv("windows-1250", "UTF-8", $json);
        $input = json_decode($json);
        foreach ($fields as $field)
        {
            if ($this->isFillable($field['name']) && isset($input[$field['name']]))
            {
                $updateA[$field['name']] = $input[$field['name']];
            }
        }
        return $updateA;
    }

    /**
     * Update a record
     * 
     * @param type $pkValue
     * @return \Model
     */
    public function editRec($pkValue, $json = null)
    {

        $pkName = $this->metaData['table']['pk_name'];

        $fields = $this->metaData['fields_array'];

        if (empty($json))
        {
            $updateA = $this->__editGet($fields);
        }
        else
        {
            $updateA = $this->__editJson($fields, $json);
        }

        //print_r($updateA);
        DB::table($this->table)->where($pkName, '=', $pkValue)->update($updateA);

        return $this;
    }

    /**
     * Setter for table
     * 
     * @param type $tableName
     */
    public function setTable($tableName)
    {
        $this->table = $tableName;
    }

    /**
     * Setter for metaData
     * 
     * @param type $tableName
     */
    public function setMetaData($tableName)
    {
        $this->metaData = Table::getTableMeta($tableName);
    }

    /**
     * Getter for metaData
     * 
     * @return type
     */
    public function getMetaData()
    {
        return $this->metaData;
    }

}

?>
