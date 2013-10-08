<?php class Domain {
    
    //a collection of tables as listed in _db_tables
    private $tables = array();
    
    private $log = array();
    
    /**
     * 
     * @param type $severity
     * @param type $message
     */
    private function __log($severity, $message) {
        $this->log[] = array("severity"=>$severity, "message"=>$message);
    }

    /**
     * Getter for $log
     * 
     * @return type
     */
    public function getLog() {
        return $this->log;
    }

    /**
     * Replace _ with spaces and make first character of each word uppercase
     * 
     * @param type $name
     */
    private function __makeLabel($name)
    {
        return ucwords(str_replace('_', ' ', $name));
    }

    /**
     * Returns varchar if fieldType = varchar(100) etc.
     */
    private function __getFieldType($fieldType) {
        $start = strpos($fieldType,'(');
        if ($start > 0) {
            $fieldType = substr($fieldType, 0, $start);
            $this->__log("success", "fieldtype : $fieldType");
        }
        return $fieldType;
    }
    
    /**
     * Returns 100 if fieldType = varchar(100) etc.
     */
    private function __getFieldLength($fieldType) {
        $start = strpos($fieldType,'(')+1;
        $len = null;
        if ($start > 0) {
            $count = strpos($fieldType,')')-$start;
            $len = substr($fieldType, $start, $count);
            //$this->__log("success", "fieldtype : $fieldType, start : $start, count : $count, len : $len");
        }

        return $len;
    }
    
    /**
     * Try and calculate the width of the widget to display the field in 
     */
    private function __getFieldWidth($fieldType, $fieldLength) {
        return 100;
    }    
    
    /**
     * Try and calculate the best widget to display the field in. Define the widget in json
     */
    private function __getFieldWidget($fieldType, $fieldLength) {
        return ""; //'{widget" : "input", "attributes" : {"type" : "text"}}';
    }    
    
}

?>
