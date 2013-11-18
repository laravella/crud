<?php use Laravella\Crud\Params;
use Laravella\Crud\DbGopher;
use Laravella\Crud\Options;

/**
 * All database requests are handled by this controller, 
 * even the DbApiController ones, although DbApiController is leaner on the response i.e. json.
 */
class DbController extends AuthorizedController {

    //protected $layout = //getLayout;
    private $log = array();

    const SUCCESS = "success";
    const INFO = "info";
    const IMPORTANT = "important";

    const HTML = "text/html";
    const XML = "text/xml";
    const JSON = "text/json";
    
    public $displayType = self::HTML;
    
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
    protected function log($severity, $message)
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

    public function getSkin() {
//        $skin = Options::getSkin();
//        $skin = array('admin'=>Options::get('skin','admin'), 'frontend'=>Options::get('skin','frontend'));
        return Options::getSkin();
    }
    
    /**
     * 
     * 
     * @param type $type Either 'admin' or 'frontend'
     * @return type
     */
    public function getLayout($type = 'admin') {
        return Options::get('skin', $type).'.default';
    }
    
    /**
     * Get the content slug which corresponds to contents.slug
     * 
     * @param type $slug
     * @return type
     */
    public function getIndex($slug = '') {
            /*
        $viewName = Options::get('skin', 'frontend').'.frontview';
        
        $contentsA = Table::asArray('contents', array('slug'=>$slug));
        
        if (isset($contentsA) && !empty($contentsA)) {
            $contentId = $contentsA[0]['id'];
            $pageA = Table::asArray('_db_pages', array('content_id'=>$contentId));
            $tableId = $pageA[0]['table_id'];

            $table = Table::asArray('_db_tables', array('id'=>$tableId));
            $tableName = $table[0]['name'];

            $actionId = $pageA[0]['action_id'];
            $actions = Table::asArray('_db_actions', array('id'=>$actionId));
            $actionName = $actions[0]['name'];

            $params = $this->__makeParams('success', '', $slug, $tableName, $actionName);

            $params->contents = $contentsA;
            $params->view = $viewName;
            $params->slug = $slug;
            $params = $params->asArray();
        } else {
            //$params = Params::bySlug(true, $slug, $viewName);
        }
        
        return View::make(Options::get('skin', 'frontend').'.frontlayout')
                ->nest('content', Options::get('skin', 'frontend').'.frontview', $params);
             * 
             */
    }
    
    /**
     * The root of the crud application /db
     * 
     * @return type
     */
//    public function getIndex($slug = '')
//    {
//        return $this->getPage('contents');
//        
//    }

    /**
     * The root of the crud application /db
     * 
     * @return type
     */
    public function getAdmin()
    {
        return $this->getPage('contents');
    }

    /**
     * 
     * @param type $page
     */
    public function getPage($page='contents') {
        
        $action = 'getPage';

        //select table data from database
        $table = DB::table($page);

        if (empty($message)) {
            $message = "Data selected.";
        }
        
        $this->log(self::SUCCESS, "$page selected");

        //get related data
        $params = $this->__makeParams(self::SUCCESS, $message, $table, $page, $action);
        
        $layout = Options::get('skin', 'frontend').'.frontlayout';
        
        return View::make($layout)->nest('content', $params->view->name, $params->asArray());    
    }
    
    /**
     * Find the right view to use with the action
     * 
     * @param type $tableName
     * @param type $action
     */
    protected function __getView($tableName, $action)
    {
        $views = DB::table('_db_pages')
                ->join('_db_tables', '_db_pages.table_id', '=', '_db_tables.id')
                ->join('_db_actions', '_db_pages.action_id', '=', '_db_actions.id')
                ->join('_db_views', '_db_pages.view_id', '=', '_db_views.id')
                ->where('_db_actions.name', '=', $action)
                ->where('_db_tables.name', '=', $tableName)
                ->first();
        return $views;
    }

    /**
     * Find the right object and view to use with the page slug
     * 
     * @param type $tableName
     * @param type $action
     */
    protected function __getSlug($slug)
    {
        $views = DB::table('_db_pages')
                ->join('_db_objects', '_db_pages.object_id', '=', '_db_objects.id')
                ->join('_db_views', '_db_pages.view_id', '=', '_db_views.id')
                ->where('_db_pages.slug', '=', $slug)
                ->first();
        return $views;
    }

    /**
     * Get a record from _db_pages as an stdClass object
     * 
     * @param type $tableName
     * @param type $viewId
     * @param type $action
     * @return type
     */
    protected function __getTableActionView($tableName, $viewId, $action)
    {
        $tva = DB::table('_db_pages')
                ->join('_db_tables', '_db_pages.table_id', '=', '_db_tables.id')
                ->join('_db_actions', '_db_pages.action_id', '=', '_db_actions.id')
                ->join('_db_views', '_db_pages.view_id', '=', '_db_views.id')
                ->select('_db_pages.view_id', '_db_pages.id as page_id', '_db_pages.action_id', 
                        '_db_views.name as view_name', '_db_actions.name as action_name', 
                        '_db_tables.name as table_name', '_db_pages.title', '_db_pages.object_id')
                ->where('_db_pages.view_id', '=', $viewId)
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
    protected function __getPermissions($tableName, $action)
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
    public function getSelect($tableName = null, $message = "")
    {
        $action = 'getSelect';

        //select table data from database
        $table = DB::table($tableName);

        if (empty($message)) {
            $message = "Data selected.";
        }
        
        $this->log(self::SUCCESS, "$tableName selected");

        //get related data
        $params = $this->__makeParams(self::SUCCESS, $message, $table, $tableName, $action);
        
        return View::make($this->getLayout())->nest('content', $params->view->name, $params->asArray());
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

    protected function __attachRelatedData($records, $ma)
    {
        $pkTables = array();
        
        return $pkTables;
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
                        
//                        $this->dbTables[$pkTableName] = array();
                    }

                    //get the actual data of the primary key related to this field (not the meta data)
                    $pkData = DB::table($pkTableName)->where($pkfName, $pkValue)->get();
                    
                    //get the value of the display field related to the pk
                    if (isset($this->dbTables[$pkTableName]['data'][$pkValue])) {
                        $pkdValue = $this->dbTables[$pkTableName]['data'][$pkValue]->$pkdfName;
                    } else {
                        $pkdValue = '';
                    }
                    
                    $pkRec[$pkValue] = $pkdValue;
                    
                    $this->log(self::INFO, "{$ma[$name]['name']} : key {$pkTableName}.{$pkfName} = {$pkValue} display : {$pkdfName} = {$pkRec[$pkValue]}");

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
     * Handle a search request and display it in the select view
     * 
     * @param type $tableName
     * @return type
     */
    public function getSearch($tableName = null, $q = null)
    {
        $action = 'getSelect';

        //get the json string from the http querystring ?q=json
//        $json = Input::get('q');
        $json = $q;

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

        return View::make($this->getLayout())->nest('content', $params->view->name, $params->asArray());
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
    protected function __makeParams($status, $message, $data, $tableName, $action)
    {

        $this->log(self::INFO, "tableName = $tableName");

        $prefix = array("id" => "/db/edit/$tableName/");

        $tables = array();
        $pkTables = array();

        $tableMeta = Table::getTableMeta($tableName);

        $view = $this->__getView($tableName, $action);

        $tableActionViews = array();
        try {
            if (is_object($view)) {
                $tableActionViews = $this->__getTableActionView($tableName, $view->id, $action);
            }
        } catch (Exception $e) {
            $tableActionViews = array();
        }

        $selects = $this->__getPkSelects($tableMeta['fields_array']);

        $this->log(self::INFO, "makeParams");

        if (is_object($data)) {
        
            $pageSize = (is_object($view))?$view->page_size:10;
            
            $paginated = $data->paginate($pageSize);

            $dataA = DbGopher::makeArray($tableMeta['fields'], $paginated);

            $tables[$tableName] = new Table($tableName, $dataA, $tableMeta);

            $pkTables = $this->__attachPkData($paginated, $tableMeta['fields_array']);

            $relatedData = $this->__attachRelatedData($paginated, $tableMeta['fields_array']);
            
            foreach ($pkTables as $pktName => $pkTable)
            {
                $tables[$pktName] = new Table($pktName, $this->dbTables[$pktName]['dataA'], $this->dbTables[$pktName]['meta']);
//                $tables[$pktName] = new Table($pktName, array(), array());
            }

            $p = new Params(false, $status, $message, $this->log, $view, $action, $tableMeta, 
                    $tableActionViews, $prefix, $selects, $this->displayType, $dataA, $tables, $paginated, $pkTables);
        
        } else {
            
            $p = new Params(false, $status, $message, $this->log, $view, $action, $tableMeta, 
                    $tableActionViews, $prefix, $selects, $this->displayType);
            
//            print_r($p);
//            die;
            
        }
        
        return $p;
    }

    /**
     * Delete a record
     * 
     * @param type $tableName
     * @param type $recorid
     * @return type
     */
    public function getDelete($tableName = null, $recorid = null)
    {
        //check for foreign key constraints
        $action = 'getDelete';
        $params = null;
        try {
            DB::table($tableName)->where('id', '=' ,$recorid)->delete();
            $params = $this->__makeParams(self::SUCCESS, "Record deleted.", null, $tableName, $action);
        } catch (Exception $e) {
            $params = $this->__makeParams(self::IMPORTANT, "Failed to delete record.", null, $tableName, $action);
        }
        $res = '{"status":"failed"}';
        if (is_object($params)) {
            $res = json_encode($params->asArray());
        }
        echo $res;
        die;
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
        
        return View::make($this->getLayout())->nest('content', $params->view->name, $params->asArray());
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

        $tableMeta = Table::getTableMeta($tableName);

        //get metadata as an array
        $pkName = $tableMeta['table']['pk_name'];

        $table = DB::table($tableName)->where($pkName, '=', $pkValue);

        $params = $this->__makeParams(self::INFO, 'Edit data.', $table, $tableName, $action);

        $paramsA = $params->asArray();

        return View::make($this->getLayout())->nest('content', $paramsA['view']->name, $paramsA);
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

        Model::getInstance($tableName)->editRec($pkValue, Input::get('data'));

        return Redirect::to("db/select/$tableName");
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
    protected function __getPkSelects($meta)
    {
        $selectA = array();
        foreach ($meta as $metaField)
        {
            if (isset($metaField['pk']))
            {
                //metadata of the primary key
                $pk = $metaField['pk'];
                
                //meta data of the field used to display the primary key
                $pkd = $pk;
                if (isset($metaField['pk_display']) && !empty($metaField['pk_display'])) {
                    $pkd = $metaField['pk_display'];
                }
                
                $selectA[$metaField['name']] = $this->__getSelect($pk['tableName'], $pk['name'], $pkd['name']);
            }
        }
        return $selectA;
    }

    /**
     * Get a select array(object(value, text))
     */
    protected function __getSelect($table, $valueField, $textField)
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
     * This is for custom front-end actions that simply needs the Params object 
     * and defines it's own view via the table_action_view table
     * 
     * @param type $parameters
     * @return type
     */
    protected function _customAction($parameters) {
        
        if (!empty($parameters)) {
            $action = 'get'.$parameters[0];
            if (count($parameters) > 1 ) {
                $tableName = $parameters[1];
                $tableMeta = Table::getTableMeta($tableName);

                //get metadata as an array
                $pkName = $tableMeta['table']['pk_name'];
                $pkName = ($tableName == 'contents')?'slug':'id';

                $table = null;

                if (isset($parameters[2])) {
                    $table = DB::table($tableName)->where($pkName, '=', $parameters[2]);
                } else if ($action == 'getRegister') {
                    $table = array();
                } else {
                    $table = DB::table($tableName);
                }

                $params = $this->__makeParams(self::INFO, 'Edit data.', $table, $tableName, $action);
                $paramsA = $params->asArray();

                if(isset($paramsA['view'])) {
                    return View::make($paramsA['view']->name)->with($paramsA);
                } else {
                    return View::make(Options::get('skin').'.default');
                }
            }
        } else {
            return View::make(Options::get('skin').'.default');
        }
    }
    
    public function getTest() {
        
//        $tables = Model::getInstance('_db_tables', array('name' => 'noTable'));
//
//        $field = Model::getInstance('_db_fields', array('id'=>1000, 'name' => 'noTable', 
//            'fullname'=>'asdf', 'label'=>'asdf', 'table_id'=>0, 'pk_field_id'=>0, 'pk_display_field_id'=>0));
//        
//        return "save";
//        $field = $tables->push($field);
//        echo var_dump($field);
    }
    
    /**
     * If method is not found
     * 
     * @param type $parameters
     * @return string
     */
    public function missingMethod($parameters)
    {
        return $this->_customAction($parameters);
//        print_r($parameters);
//        return "missing";
    }

}

?>
