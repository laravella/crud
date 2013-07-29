<?php

use Laravella\Crud\Log;

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
    public function getReinstall()
    {

        set_time_limit(360);

        foreach (DbInstallController::__getAdminTableClasses(true) as $adminTableClass)
        {
            $atc = new $adminTableClass();
            $atc->down();
            Log::write("success", "dropped table $adminTableClass");
        }

//        foreach (DbInstallController::__getAdminTables(true) as $adminTable)
//        {
//            Schema::dropIfExists($adminTable);
//            Log::write("success", "dropped table $adminTable");
//        }
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
     * returns an array with a list of tables that are used for admin purposes
     * 
     * @param type $dropSafe Set dropSafe = true if tables should be returned in an order that is safe to drop them 
     * @return type String[]
     */
    private static function __getAdminTableClasses($dropSafe = false)
    {
        if (!$dropSafe)
        {   //order in which to create tables
            return array(
                "CreateLogsTable",
                "CreateUsergroupsTable",
                "CreateUsersTable",
                "CreateSeveritiesTable",
                "CreateAuditTable",
                "CreateTablesTable",
                "CreateFieldsTable",
                "CreateViewsTable",
                "CreateActionsTable",
                "CreateTableActionViewsTable",
                "CreateUserPermissionsTable",
                "CreateUserGroupPermissionsTable");
        }
        else
        {   //order in which to drop tables
            return array(
                "CreateLogsTable",
                "CreateSeveritiesTable",
                "CreateAuditTable",
                "CreateTableActionViewsTable",
                "CreateUserPermissionsTable",
                "CreateUserGroupPermissionsTable",
                "CreateFieldsTable",
                "CreateViewsTable",
                "CreateActionsTable",
                "CreateTablesTable",
                "CreateUsersTable",
                "CreateUsergroupsTable");
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
            foreach (DbInstallController::__getAdminTableClasses() as $adminTableClass)
            {
                $atc = new $adminTableClass();
                $atc->up();
            }

            $dbSeeder = new DatabaseSeeder();
            $dbSeeder->run();

            Log::write("success", "Installation completed successfully.");
        }
        catch (Exception $e)
        {
            Log::write("important", $e->getMessage());
            $message = " x Error during installation.";
            Log::write("important", $message);
//throw new Exception($message, 1, $e);
        }
        //$totalLog = array_merge($domain->getLog(), $this->log);
        return View::make("crud::dbinstall", array('action' => 'install', 'log' => array()));
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