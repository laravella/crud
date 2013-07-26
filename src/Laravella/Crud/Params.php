<?php

namespace Laravella\Crud;

class Params {

    public $action = "";
    public $tableMeta = null;
    public $tables = null;
    public $paginated = null;
    public $primaryTables = array();
    public $prefix = "";
    public $tableActionViews = null;
    public $view = null;
    public $selects = array();
    public $log = array();
    public $status = "success";

    /**
     * 
     * Used to pass a consistent set of data to views and prevent "$variable not found" errors.
     * 
     * @param type $status Wether the action succeeded or not.  See log for further details.
     * @param type $action the action that controller is performing. See _db_actions.name 
     * @param type $tableMeta The table's meta data. As generated by Laravella\Crud\Table::getTableMeta()
     * @param type $tables Is an array of Table objects. Actual data.
     * @param type $pageSize. The size of the pagination.
     * @param type $primaryTables A list of records with primary keys related to this table's via foreign keys.
     * @param type $prefix Used to prepend the href on the primary key
     * @param type $view An entry in _db_views
     */
    public function __construct($status, $message, $log, $view = null, $action = "", $tableMeta = null, $tableActionViews = null, $prefix = "", $selects = null, $tables = null, $paginated = null, $primaryTables = null)
    {
        $this->status = $status;
        $this->message = $message;
        $this->paginated = $paginated;
        $this->action = $action;
        $this->tableMeta = $tableMeta;
        $this->tables = $tables;
        $this->pageSize = $view->page_size;
        $this->primaryTables = $primaryTables;
        $this->prefix = $prefix;
        $this->tableActionViews = $tableActionViews;
        $this->view = $view;
        $this->selects = $selects;
        $this->log = $log;
    }

    /**
     * For Edit
     * 
     * @param type $status
     * @param type $message
     * @param type $log
     * @param type $view
     * @param type $action
     * @param type $tableMeta
     * @param type $tableActionViews
     * @param type $prefix
     * @param type $selects
     * @param type $tables
     * @param type $paginated
     * @param type $primaryTables
     * @return \Laravella\Crud\Params
     */
    public static function forEdit($status, $message, $log, $view = null, 
            $action = "", $tableMeta = null, $tableActionViews = null, 
            $prefix = "", $selects = null, 
            $tables = null, $paginated = null, $primaryTables = null)
    {
        $params = new Params();
        return $params;
    }

    /*
     * meta
     * data
     * name
     * pagesize
     * selects
     */

    public function asArray()
    {

        $returnA = array("action" => $this->action,
            "meta" => $this->tableMeta['fields_array'],
            "tables" => $this->tables,
            "data" => $this->paginated,
            "tableName" => $this->tableMeta['table']['name'],
            "prefix" => $this->prefix,
            "pageSize" => $this->pageSize,
            "pkTables" => $this->primaryTables,
            "view" => $this->view,
            "selects" => $this->selects,
            "log" => $this->log,
            "status" => $this->status,
            "message" => $this->message,
            "pkName" => $this->tableMeta['table']['pk_name']); //$this->tables[$tableName]['tableMetaData']['table']['pk_name']);

        if (isset($this->tableActionViews) && is_object($this->tableActionViews))
        {
            $returnA["title"] = $this->tableActionViews->title;
        }
        else
        {
            $returnA["title"] = "";
        }

        $returnA['params'] = json_encode($returnA);

        return $returnA;
    }

}

?>