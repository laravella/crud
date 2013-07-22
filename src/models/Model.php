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
