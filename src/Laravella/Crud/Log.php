<?php class Log {

    const SUCCESS = "success";
    const INFO = "info";
    const IMPORTANT = "important";

    /**
     * 
     * @param type $severity
     * @param type $message
     */
    public static function write($severity, $message) {
        $entry = array("severity"=>$severity, "message"=>$message);
        $id = DB::table('_db_log')->insertGetId($entry);
        return $id;
    }

    /**
     * Getter for $log
     * 
     * @return type
     */
    public static function getLog() {
        return DB::table('_db_log')->get();
    }
        
}

?>
