<?php

use Laravella\Crud\Params;

class DbController extends Controller {

    protected $layout = 'crud::layouts.default';
    private $log = array();

    const SUCCESS = "success";
    const INFO = "info";
    const IMPORTANT = "important";

    /**
     * A cache of db tables to minimize db requests. See getPkSelects()
     * 
     * @var type 
     */
    private $dbTables = array();

    /**
     * 
     * @param type $severity
     * @param type $message
     */
    private function log($severity, $message)
    {
        $this->log[] = array("severity" => $severity, "message" => $message);
    }

    /**
     * Getter for $log
     * 
     * @return type
     */
    public function getLog()
    {
        return $this->log();
    }

    /**
     * The root of the crud application /db
     * 
     * @return type
     */
    public function getIndex()
    {
        $action = 'getIndex';

        return View::make("crud::dbinstall", array('action' => $action));
    }

    /**
     * Find the right view to use with the action
     * 
     * @param type $tableName
     * @param type $action
     */
    private function __getView($tableName, $action)
    {
        $views = DB::table('_db_table_action_views')
                ->join('_db_tables', '_db_table_action_views.table_id', '=', '_db_tables.id')
                ->join('_db_actions', '_db_table_action_views.action_id', '=', '_db_actions.id')
                ->join('_db_views', '_db_table_action_views.view_id', '=', '_db_views.id')
                ->where('_db_actions.name', '=', $action)
                ->where('_db_tables.name', '=', $tableName)
                ->first();
        return $views;
    }

    /**
     * Get a record from _db_table_action_views as an stdClass object
     * 
     * @param type $tableName
     * @param type $viewId
     * @param type $action
     * @return type
     */
    private function __getTableActionView($tableName, $viewId, $action)
    {
        $tva = DB::table('_db_table_action_views')
                ->join('_db_tables', '_db_table_action_views.table_id', '=', '_db_tables.id')
                ->join('_db_actions', '_db_table_action_views.action_id', '=', '_db_actions.id')
                ->where('_db_table_action_views.view_id', '=', $viewId)
                ->where('_db_actions.name', '=', $action)
                ->where('_db_tables.name', '=', $tableName)
                ->first();
        return $tva;
    }

    /**
     * Check permissions
     * 
     * @param type $tableName
     * @param type $action
     */
    private function __getPermissions($tableName, $action)
    {
        //
        return true;
    }

    /**
     * /db/select/{tablename}
     * 
     * @param type $table
     * @return type
     */
    public function getSelect($tableName = null)
    {
        $action = 'getSelect';

        //select table data from database
        $table = DB::table($tableName);

        $this->log(self::SUCCESS, "$tableName selected");

        //get related data
        $params = $this->__makeParams(self::SUCCESS, "Data selected.", $table, $tableName, $action);

        return View::make($params->view->name, $params->asArray());
    }

    /**
     * Index an array of records (of type StdClass) according to the pk value
     */
    private function __indexByPk($array, $pkFieldName)
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
    private function __indexByValue($array, $fieldName)
    {
        $newArray = array();
        foreach ($array as $key => $rec)
        {
            $newArray[$rec[$fieldName]] = $rec;
        }
        return $newArray;
    }

    /**
     * 
     * 
     * @param type $records
     * @param type $ma
     * @return array
     */
    private function __attachPkData($records, $ma)
    {
        $pkTables = array();
        $pkRec = array();

        foreach ($records as $record)
        {
            foreach ($record as $name => $value)
            {
                if (isset($ma[$name]['pk']))
                {
                    $this->log(self::INFO, "{$ma[$name]['name']} has a pk");
                    //$name is a foreign key, it contains a reference to a primary key
                    //pk display field's meta data array
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
                    }

                    //get the actual data of the primary key related to this field (not the meta data)
                    //$pkData = DB::table($pkTableName)->where($pkfName, $pkValue)->get();
                    //get the value of the display field related to the pk
                    $pkRec[$pkValue] = $this->dbTables[$pkTableName]['data'][$pkValue]->$pkdfName;

                    $this->log(self::INFO, "{$ma[$name]['name']} : key {$pkTableName}.{$pkfName} = {$pkValue} display : {$pkdfName} = {$pkRec[$pkValue]}");

                    if (!array_key_exists($pkTableName, $pkTables))
                    {
                        $pkTables[$pkTableName] = array();
                    }

                    $pkTables[$pkTableName][$pkValue] = $this->dbTables[$pkTableName]['data'][$pkValue]->$pkdfName;
                }
            }
        }
//        print_r($pkTables);
        return $pkTables;
    }

    /**
     * Handle a search request and display it in the select view
     * 
     * @param type $tableName
     * @return type
     */
    public function getSearch($tableName = null)
    {
        $action = 'getSelect';

        //get the json string from the http querystring ?q=json
        $json = Input::get('q');

        $searchObj = json_decode($json, true);

        foreach ($searchObj as $sTable => $sFields)
        {
            $table = DB::table($sTable);

            foreach ($sFields as $sField => $sValue)
            {
                $table->where($sField, '=', $sValue);
            }
        }

        $params = $this->__makeParams(self::SUCCESS, "Records selected.", $table, $tableName, $action);

        return View::make($params->view->name, $params->asArray());
    }

    /**
     * Create a standard params object that will be passed to the view.
     * 
     * Data is not fetched yet, use data->get(), or data->paginate() to fetch
     * 
     * @param type $data
     * @param type $tableName
     * @param type $action
     * @return \Laravella\Crud\Params
     */
    private function __makeParams($status, $message, $data, $tableName, $action)
    {

        $prefix = array("id" => "/db/edit/$tableName/");

        $tables = array();
        $pkTables = array();

        $tableMeta = Table::getTableMeta($tableName);

        $view = $this->__getView($tableName, $action);

        $tableActionViews = $this->__getTableActionView($tableName, $view->id, $action);

        $selects = $this->__getPkSelects($tableMeta['fields_array']);

        $this->log(self::INFO, "makeParams");

        if (is_object($data))
        {

            $paginated = $data->paginate($view->page_size);

            $dataA = DbGopher::makeArray($tableMeta['fields'], $paginated);

            $tables[$tableName] = new Table($tableName, $dataA, $tableMeta);

            $pkTables = $this->__attachPkData($paginated, $tableMeta['fields_array']);

            foreach ($pkTables as $pktName => $pkTable)
            {
                $tables[$pktName] = new Table($pktName, $this->dbTables[$pktName]['dataA'], $this->dbTables[$pktName]['meta']);
            }

            return new Params($status, $message, $this->log, $view, $action, $tableMeta, $tableActionViews, $prefix, $selects, $tables, $paginated, $pkTables);
        }
        else
        {

            $p = new Params($status, $message, $this->log, $view, $action, $tableMeta, $tableActionViews, $prefix, $selects);

            return $p;
        }
    }

    /**
     * Prompt user to insert a new record
     * 
     * @param type $table
     * @param type $pkValue
     * @return type
     */
    public function getInsert($tableName = null)
    {
        $action = 'getInsert';

        $params = $this->__makeParams(self::INFO, "Enter data to insert.", null, $tableName, $action);

        return View::make($params->view->name, $params->asArray());
    }

    /**
     * Display a single record on screen to be edited by the user
     * 
     * @param type $table
     * @param type $pkValue
     * @return type
     */
    public function getEdit($tableName = null, $pkValue = 0)
    {
        $action = 'getEdit';

        $model = Model::getInstance($tableName);
        
        $tableMeta = $model->getMetaData();
        
        //get metadata as an array
        $metaA = $tableMeta['fields_array'];
        $meta = $tableMeta['fields'];
        $pkName = $tableMeta['table']['pk_name'];

        $data = DB::table($tableName)->where($pkName, '=', $pkValue)->get();

        $prefix = array();

        $dataA = DbGopher::makeArray($meta, $data);

        $selects = $this->__getPkSelects($metaA);

        $view = $this->__getView($tableName, $action)->name;

/*        
        $params = $this->__makeParams(self::INFO, 'Edit data.', $table, $tableName, $action);
        
        $paramsA = $params->asArray();

        $model = Model::getInstance($tableName);
        $tableMeta = $model->getMetaData($tableName);
        $meta = $tableMeta['fields'];
        
        $dataA = DbGopher::makeArray($meta, $paramsA['data']);
        
        $paramsA['data'] = $paramsA['data'][0];
        */
                
        
/*
 * 
action
meta
x tables = array(tableName => Table)
x data = paginate
tableName
x prefix = array(fieldName => String)
x pageSize = int
x pkTables
x view = $dbController->__getView($tableName, $action);
selects
x log = array(array('severity'=>String, 'message'=>String))
status
message
pkName
x title

 */        
        
$params = array('action' => $action,
                    'data' => $dataA[0],
                    'meta' => $metaA,
                    'pkName' => $pkName,
                    'prefix' => $prefix,
                    'selects' => $selects,
                    'tableName' => $tableName,
                    'status' => 'info',
                    'message' => 'Edit data.',
                    'log' => array());

$params['params'] = json_encode($params);
        
//print_r($params);
//die;
        
        return View::make($view, $params);
    }

    /**
     * Update data to the database
     * 
     * @param type $tableName
     * @param type $id
     * @return type
     */
    public function postEdit($tableName = null, $pkValue = null)
    {
        $action = 'postEdit';

        Model::getInstance($tableName)->editRec($pkValue);

        return Redirect::to("/db/edit/$tableName/$pkValue");
    }

    /**
     * Insert a new record into $tableName
     */
    public function postInsert($tableName)
    {

        $action = 'postInsert';

        $id = Model::getInstance($tableName)->insertRec();

        return Redirect::to("/db/edit/$tableName/$id");
    }

    /**
     * Loop through foreign keys and generate an array of select boxes for each
     * related primary key
     * 
     * @param type $meta
     */
    private function __getPkSelects($meta)
    {
        $selectA = array();
        foreach ($meta as $metaField)
        {
            if (isset($metaField['pk']))
            {
                //metadata of the primary key
                $pk = $metaField['pk'];
                //meta data of the field used to display the primary key
                $pkd = $metaField['pk_display'];
                $selectA[$metaField['name']] = $this->__getSelect($pk['tableName'], $pk['name'], $pkd['name']);
            }
        }
        return $selectA;
    }

    /**
     * Get a select array(object(value, text))
     */
    private function __getSelect($table, $valueField, $textField)
    {
        $data = DB::table($table)->select($valueField, $textField)->get();
        $arr = array();
        if (is_array($data))
        {
            foreach ($data as $rec)
            {
                $arr[$rec->$valueField] = array('value' => $rec->$valueField, 'text' => $rec->$textField);
            }
        }
        return $arr;
    }

    /**
     * If method is not found
     * 
     * @param type $parameters
     * @return string
     */
    public function missingMethod($parameters)
    {
        print_r($parameters);
        return "missing";
    }

}

?>
