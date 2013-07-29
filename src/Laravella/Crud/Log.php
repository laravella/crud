<?php

namespace Laravella\Crud;

class Log {

    const SUCCESS = "success";
    const INFO = "info";
    const IMPORTANT = "important";

    /**
     * 
     * @param type $severity
     * @param type $message
     */
    public static function write($severity, $message)
    {
        echo $message."\n";
        $id = null;
        try
        {
            if (\Schema::hasTable('_db_logs'))
            {

                $entry = array("severity" => $severity, "message" => $message);
                $id = \DB::table('_db_logs')->insertGetId($entry);
            }
        }
        catch (Exception $e)
        {
            //
        }
        return $id;
    }

    /**
     * Getter for $log
     * 
     * @return type
     */
    public static function getLog()
    {
        return DB::table('_db_log')->get();
    }

}

?>
