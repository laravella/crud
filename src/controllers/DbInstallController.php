<?php

class DbInstallController extends Controller {

    protected $layout = 'crud::layouts.default';

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
    public function getReinstall(&$log = array())
    {
        foreach (DbInstallController::__getAdminTables(true) as $adminTable)
        {
            Schema::dropIfExists($adminTable);
            $log[] = "dropped table $adminTable";
        }
        return $this->getInstall($log);
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
            return array("_db_tables",
                "_db_fields",
                "_db_views",
                "_db_actions",
                "_db_table_action_views",
                "_db_user_permissions",
                "_db_usergroup_permissions");
        }
        else
        {
            return array("_db_table_action_views",
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
    public function getInstall(&$log = array())
    {
        try
        {
//create all the tables
            $domain = new Domain();
            foreach (DbInstallController::__getAdminTables() as $adminTable)
            {
                $domain->create($adminTable, $log);
            }

            try
            {
                $domain->populate($log);
                $domain->updateReferences($log);
            }
            catch (Exception $e)
            {
                $log[] = $e->getMessage();
                $message = " x Error populating tables.";
                $log[] = $message;
                throw new Exception($message, 1, $e);
            }
            $log[] = "Installation completed successfully.";
        }
        catch (Exception $e)
        {
            $log[] = $e->getMessage();
            $message = " x Error during installation.";
            $log[] = $message;
//throw new Exception($message, 1, $e);
        }
        return View::make("crud::dbinstall", array('action' => 'install', 'log' => $log));
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