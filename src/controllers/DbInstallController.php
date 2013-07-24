<?php

class DbInstallController extends Controller {

    protected $layout = 'crud::layouts.default';
    
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
        return $this->log();
    }
    
    /**
     * The root of the crud application /db
     * 
     * @return type
     */
    public function getIndex()
    {
        return View::make("crud::dbinstall", array('action' => 'index'));
    }

    /**
     * Drop metadata tables and redo an install
     */
    public function getReinstall()
    {
        foreach (DbInstallController::__getAdminTables(true) as $adminTable)
        {
            Schema::dropIfExists($adminTable);
            $this->__log("success", "dropped table $adminTable");
        }
        return $this->getInstall();
    }

    /**
     * returns an array with a list of tables that are used for admin purposes
     * 
     * @param type $dropSafe Set dropSafe = true if tables should be returned in an order that is safe to drop them 
     * @return type String[]
     */
    private static function __getAdminTables($dropSafe = false)
    {
        if (!$dropSafe)
        {
            return array(
                "_db_severities",
                "_db_logs",
                "_db_audit",
                "_db_tables",
                "_db_fields",
                "_db_views",
                "_db_actions",
                "_db_table_action_views",
                "_db_user_permissions",
                "_db_usergroup_permissions");
        }
        else
        {
            return array(
                "_db_logs",
                "_db_severities",
                "_db_audit",
                "_db_table_action_views",
                "_db_user_permissions",
                "_db_usergroup_permissions",
                "_db_fields",
                "_db_views",
                "_db_actions",
                "_db_tables");
        }
    }

    /**
     * Generate metadata from the database and insert it into _db_tables
     * 
     * @param type $table
     * @return type
     */
    public function getInstall()
    {
        try
        {
            set_time_limit(360);
//create all the tables
            $domain = new Domain();
            foreach (DbInstallController::__getAdminTables() as $adminTable)
            {
                $domain->create($adminTable);
            }

            try
            {
                $domain->populate();
                $domain->updateReferences();
            }
            catch (Exception $e)
            {
                $this->__log("important", $e->getMessage());
                $message = " x Error populating tables.";
                $this->__log("success", $message);
                throw new Exception($message, 1, $e);
            }
            $this->__log("success", "Installation completed successfully.");
        }
        catch (Exception $e)
        {
            $this->__log("important", $e->getMessage());
            $message = " x Error during installation.";
            $this->__log("important", $message);
//throw new Exception($message, 1, $e);
        }
        $totalLog = array_merge($domain->getLog(), $this->log);
        return View::make("crud::dbinstall", array('action' => 'install', 'log' => $totalLog));
    }

    /**
     * If method is not found
     * 
     * @param type $parameters
     * @return string
     */
    public function missingMethod($parameters)
    {
        return "missing";
    }

}

?>