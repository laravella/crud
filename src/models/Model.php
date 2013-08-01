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
        $model->setGuarded(array($model->metaData['table']['pk_name']));
        return $model;
    }

    public function setGuarded($guardedA) {
        $this->guarded = $guardedA;
    }
    
    public function getA() {
        
    }

    /**
     * Insert a new record
     */
    public function insertRec() {
        $fields = $this->metaData['fields_array'];
        
        $updateA = array();
        foreach($fields as $field) 
        {
            if ($this->isFillable($field['name'])) {
                $updateA[$field['name']] = Input::get($field['name']);
            }
        }
        
        $id = DB::table($this->tableName)->insertGetId($updateA);
        
        return $id;
        
    }
    
    /**
     * Create an array of key values from http GET data
     * 
     * @param type $fields
     */
    private function __editGet($fields) {
        $updateA = array();
        foreach($fields as $field) 
        {
            if ($this->isFillable($field['name'])) {
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
    private function __editJson($fields, $json) {
        die;
        
        $updateA = array();
        //print_r($json);
        $json=iconv("windows-1250","UTF-8",$json);
        $input = json_decode($json);
        foreach($fields as $field) 
        {
            if ($this->isFillable($field['name']) && isset($input[$field['name']])) {
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
    public function editRec($pkValue, $json = null) {
        
        $pkName = $this->metaData['table']['pk_name'];
        
        $fields = $this->metaData['fields_array'];
        
        if (empty($json)) {
            $updateA = $this->__editGet($fields);
        } else {
            $updateA = $this->__editJson($fields, $json);
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
        $this->metaData = Table::getTableMeta($tableName);
    }

    /**
     * Getter for metaData
     * 
     * @return type
     */
    public function getMetaData() {
        return $this->metaData;
    }

}

?>
