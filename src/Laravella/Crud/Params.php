<?php

namespace Laravella\Crud;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use \Table;
use \Log;
use \Model;

/**
 * 
 * Used to pass a consistent set of data to views and prevent "$variable not found" errors.
 * 
 */
class Params extends CrudSeeder {

    public $action = "";
    public $tableMeta = null;
    public $tables = null;
    public $dataA = array();
    public $paginated = null;
    public $primaryTables = array();
    public $prefix = "";
    public $page = null; //the junction between table, view and action
    public $contents = array(); // the text contents of the page
    public $assets = null;
    public $view = null;
    public $layout = null;
    public $frontend = false;
    public $skin = null;
    public $selects = array();
    public $log = array();
    public $status = "success";
    public $slug = "";      //_db_pages.slug
    public $displayType = "text/html";
    public $displayTypes = array();
    public $widgetTypes = array();
    public $menu = array();
    public $user = null;
    public $title = '';

    /**
     * A cache of db tables to minimize db requests. See getPkSelects()
     * 
     * @var type 
     */
    private $dbTables = array();

    /**
     * $slug pageslug
     * 
     * @param type $slug
     */
    public static function bySlug($frontend, $slug, $displayType, $view)
    {

        $contentsA = Table::asArray('contents', array('slug' => $slug));
        //see if contents.id links to _db_pages.content_id to fetch relevant data
        if (isset($contentsA) && !empty($contentsA) && isset($contentsA[0]['id']))
        {
            $contentId = $contentsA[0]['id'];
            $tav = Model::getPageData(null, null, null, $contentId);

            $tableName = DbGopher::coalesce($tav, 'table_name');
            $actionName = DbGopher::coalesce($tav, 'action_name');

            $data = DB::table($tableName);

            $params = new Params($tableName, $actionName, $displayType, $data, true);

            $params->contents = $contentsA;
            //            $params->view = $viewName;
            $params->slug = DbGopher::coalesce($tav, 'slug');
        }
        else
        {
            var_dump($contentsA[0]);
            
            DbGopher::backtrace();
            die;
//            throw new \Exception($slug.' not found');
        }

        $params->contents = $contentsA;
        $params->view = $view;
        $params->slug = $slug;
        return $params;
    }

    /**
     * Create a standard params object that will be passed to the view.  The params object (instance of Laravella\Crud\Params
     * is at the heart of every view and contains all the variables that will be passed to the view, from the DbController.
     * 
     * Data is not fetched yet, use data->get(), or data->paginate() to fetch
     * 
     * @param type $data
     * @param type $tableName
     * @param type $action
     * @return \Laravella\Crud\Params
     */
    public function __construct($tableName, $action, $displayType, $data = null, $frontend = false, $contentSlug = 'contents_getpage')
    {
        Log::info("constructing Params for : tableName = $tableName");

        $prefix = array("id" => "/db/edit/$tableName/");

        $tables = array();
        $dataA = array();
        $pkTables = array();
        $paginated = array();

        $tableMeta = Table::getTableMeta($tableName);
        $view = Model::getViewData($tableName, $action);
        $tableActionViews = Model::getPageData($tableName, null, $action);
        $selects = Model::getPkSelects($tableMeta['fields_array']);

        if (is_object($data))
        {
            $pageSize = DbGopher::coalesce($view, 'page_size', 10);
            $paginated = $data->paginate($pageSize);
            $dataA = DbGopher::makeArray($tableMeta['fields'], $paginated);
            $tables[$tableName] = new Table($tableName, $dataA, $tableMeta);
            $pkTables = $this->__attachPkData($paginated, $tableMeta['fields_array']);
            $relatedData = $this->__attachRelatedData($paginated, $tableMeta['fields_array']);

            foreach ($pkTables as $pktName => $pkTable)
            {
                $tables[$pktName] = new Table($pktName, $this->dbTables[$pktName]['dataA'], $this->dbTables[$pktName]['meta']);
            }
        }

        $this->user = Auth::user();
        $this->status = 'success';
        $this->message = '';
        $this->action = $action;
        $this->tableMeta = $tableMeta;
        if (is_object($view))
        {
            $this->pageSize = $view->page_size;
        }
        else
        {
            $this->pageSize = 10;
        }

        $this->prefix = $prefix;
        $this->page = $tableActionViews;  //single, called by first()
        $this->view = $view;
        $this->frontend = $frontend;

        $this->skin();

        $this->selects = $selects;
        $this->displayType = $displayType;
        $this->log = array();
        //potentially null
        $this->paginated = $paginated;
        $this->tables = $tables;
        $this->primaryTables = $pkTables;
        $this->assets = Model::getAssets($this->page);
        $this->dataA = $dataA;
        $this->displayTypes = $this->__getDisplayTypes();
        $this->widgetTypes = $this->__getWidgetTypes();

        if (Auth::check())
        {
            $userId = Auth::user()->id;
            $this->menu = Model::getUserMenu($userId);
        }
    }

    public function skin()
    {
        $skins = Options::getSkin();
//        p($skins);
//        die;
        $this->skin = array();

        if ($this->frontend)
        {
            $this->skin['name'] = $skins['name'];
            $this->skin['fullname'] = $skins['frontend'];
            $this->skin['vendor'] = $skins['vendor'];
            $this->skin['package'] = $skins['package'];
            $this->layout = $skins['frontend'] . '.frontlayout';
        }
        else
        {
            $this->skin['name'] = $skins['adminName'];
            $this->skin['fullname'] = $skins['admin'];
            $this->skin['vendor'] = $skins['adminVendor'];
            $this->skin['package'] = $skins['adminPackage'];
            $this->layout = $skins['admin'] . '.default';
        }
    }

    /**
     * get all entries from _db_display_types
     * This determines under which conditions a field will be displayed
     * 
     * @return type
     */
    private function __getDisplayTypes()
    {
        $displayTypes = DB::table('_db_display_types')->get();
        $dtA = array();
        foreach ($displayTypes as $displayType)
        {
            $dtA[$displayType->id] = $displayType->name;
        }
        return $dtA;
    }

    /**
     * get all entries from _db_widget_types
     * This determines how a field will be displayed, what it will look like
     * 
     * @return type
     */
    private function __getWidgetTypes()
    {
        $widgetTypes = DB::table('_db_widget_types')->get();
        $dtA = array();
        foreach ($widgetTypes as $widgetType)
        {
            $dtA[$widgetType->id] = $widgetType->name;
        }
        return $dtA;
    }

    /**
     * Instantiate a Params object to use for Editing
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
    public static function forEdit($status = "success", $message = "", $log = array(), $view = null, $action = "", $tableMeta = null, $tableActionViews = null, $prefix = "", $selects = null, $displayType = "text/html", $tables = null, $paginated = null, $primaryTables = null)
    {
        $params = new Params(false, $log);
        return $params;
    }

    /**
     * MOVE TO MODEL
     */
    public function getObject($pageId)
    {
        $object = DB::table('_db_pages as p')
                        ->join('_db_page_tables as pt', 'pt.page_id', '=', 'p.id')
                        ->join('_db_tables as t', 'pt.table_id', '=', 't.id')
                        ->select('p.id as page_id', 'p.slug as slug', 't.id as table_id', 't.name as table_name')
                        ->where('p.id', $pageId)->get();
        return $object;
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

        $viewName = (is_object($this->view)) ? $this->view->name : $this->view;

        $returnA = array("action" => $this->action,
            "meta" => $this->tableMeta['fields_array'],
            "tableName" => $this->tableMeta['table']['name'],
            "prefix" => $this->prefix,
            "pageSize" => $this->pageSize,
            "view" => $viewName,
            "layout" => $this->layout,
            "frontend" => $this->frontend,
            "skin" => $this->skin,
            "slug" => $this->slug,
            "selects" => $this->selects,
            "contents" => $this->contents,
            "log" => array(), //too big
            "status" => $this->status,
            "message" => $this->message,
            "pkName" => $this->tableMeta['table']['pk_name'],
            "displayType" => $this->displayType,
            "tables" => $this->tables,
            "data" => $this->paginated,
            "dataA" => $this->dataA,
            "pkTables" => $this->primaryTables,
            "menu" => $this->menu,
            "assets" => $this->assets,
            "displayTypes" => $this->displayTypes,
            "widgetTypes" => $this->widgetTypes,
            "user" => $this->user
        ); //$this->tables[$tableName]['tableMetaData']['table']['pk_name']);

        if (isset($this->page) && is_object($this->page))
        {
            $returnA["title"] = $this->page->title;
            $returnA["object"] = $this->getObject($this->page->page_id);
        }
        if (!isset($returnA["title"]) || empty($returnA["title"]))
        {
            $returnA["title"] = $this->title;
        }

        if (Options::get('debug'))
        {
            $returnA['params'] = json_encode($returnA);
        }

        return $returnA;
    }

    protected function __attachRelatedData($records, $ma)
    {
        $fkTables = array();
        return $fkTables;
    }

    /**
     * 
     * 
     * @param type $records
     * @param type $ma
     * @return array
     */
    protected function __attachPkData($records, $ma)
    {
        $pkTables = array();
        $pkRec = array();

        foreach ($records as $record)
        {
            foreach ($record as $name => $value)
            {
                if (isset($ma[$name]['pk']))
                {
                    Log::info("{$ma[$name]['name']} has a pk");
                    //$name is a foreign key, it contains a reference to a primary key
                    //pk display field's meta data array
//                    p($ma[$name]);
//                    die;
                    $pkdfMetaA = $ma[$name]['pk_display'];

                    //pk meta data array
                    $pkfMetaA = $ma[$name]['pk'];

                    //get the name of the display field
                    $pkdfName = $pkdfMetaA['name'];

                    //get the name of the pk field
                    $pkfName = $pkfMetaA['name'];

                    //the primary key's tablename
                    $pkTableName = $ma[$name]['pk']['tableName'];

                    //the value of the foreign key, which is also the value of the pk we are looking for
                    $pkValue = $value;

                    if (!array_key_exists($pkTableName, $this->dbTables))
                    {
                        $pkData = DB::table($pkTableName)->get();
                        $pkData = $this->__indexByPk($pkData, $pkfName);
                        $pktMeta = Table::getTableMeta($pkTableName);
//an array of 
                        $pkDataA = DbGopher::makeArray($pktMeta['fields'], $pkData);
                        $this->dbTables[$pkTableName] = array('data' => $pkData, 'meta' => $pktMeta, 'dataA' => $pkDataA);
//                        $this->dbTables[$pkTableName] = array();
                    }

                    //get the actual data of the primary key related to this field (not the meta data)
                    $pkData = DB::table($pkTableName)->where($pkfName, $pkValue)->get();

                    //get the value of the display field related to the pk
                    if (isset($this->dbTables[$pkTableName]['data'][$pkValue]))
                    {
                        $pkdValue = $this->dbTables[$pkTableName]['data'][$pkValue]->$pkdfName;
                    }
                    else
                    {
                        $pkdValue = '';
                    }

                    $pkRec[$pkValue] = $pkdValue;
                    Log::info("{$ma[$name]['name']} : key {$pkTableName}.{$pkfName} = {$pkValue} display : {$pkdfName} = {$pkRec[$pkValue]}");
                    if (!array_key_exists($pkTableName, $pkTables))
                    {
                        $pkTables[$pkTableName] = array();
                    }
                    $pkTables[$pkTableName][$pkValue] = $pkdValue;
                }
            }
        }
//        print_r($pkTables);
        return $pkTables;
    }

    /**
     * Index an array of records (of type StdClass) according to the pk value
     */
    protected function __indexByPk($array, $pkFieldName)
    {
        $newArray = array();
        foreach ($array as $key => $rec)
        {
            $newArray[$rec->$pkFieldName] = $rec;
        }
        return $newArray;
    }

    /**
     * Index an array of records according to the value of $fieldName
     */
    protected function __indexByValue($array, $fieldName)
    {
        $newArray = array();
        foreach ($array as $key => $rec)
        {
            $newArray[$rec[$fieldName]] = $rec;
        }
        return $newArray;
    }

}

?>
