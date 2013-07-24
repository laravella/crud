<?php
use Laravella\Crud\Params;

class DbController extends Controller {

    protected $layout = 'crud::layouts.default';

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

        //get related data
        $params = $this->__makeParams($table, $tableName, $action);
        
        return View::make($params->view->name, $params->asArray());
    }

    /**
     * 
     * 
     * @param type $table
     * @param type $ma
     * @return array
     */
    private function __attachPkData($table, $ma) {
        $pkTables = array();
            $pkRec = array();
        
        foreach ($table as $record)
        {
            foreach ($record as $name => $value)
            {
                
                if (isset($ma[$name]['pk']))
                {
                    //{{-- $name is a foreign key, it contains a reference to a primary key --}}
                    $pkFieldId = $ma[$name]['pk_field_id'];
                    $pkDisplayFieldId = $ma[$name]['pk_display_field_id'];
                    
                    //pk display field's meta data array
                    $pkdfMetaA = $ma[$name]['pk_display'];
                    
                    //pk meta data array
                    $pkfMetaA = $ma[$name]['pk'];
                    
                    //get the name of the display field
                    $pkdfName = $pkdfMetaA['name'];
                    
                    //get the name of the pk field
                    $pkfName = $pkfMetaA['name'];
                    
                    $pkTableName = $ma[$name]['pk']['tableName'];
                    
                    $pkValue = $value;
                    
                    //get the actual data of the primary key related to this field (not the meta data)
                    $pkData = DB::table($pkTableName)->where($pkfName, $pkValue)->get();
                    
                    //$pktMeta = Model::getFieldMeta($pkTableName);
                    
                    $pktMeta = Table::getMeta($pkTableName);
                    
                    //an array of 
                    $pkDataA = Table::makeArray($pktMeta, $pkData);
                    
                    $pkDisplayValue = $pkData[0]->$pkdfName;
                    
                    $pkRec[$pkValue] = $pkDisplayValue;
                    
                }
                if (!empty ($pkRec)) {
                    /*
                    if (!isset($pkTables[$pkTableName])) {
                        $pkTables[$pkTableName] = array();
                    }
                    */
                    $pkTables[$pkTableName] = $pkRec;
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

        $params = $this->__makeParams($table, $tableName, $action);
        
        return View::make($params->view->name, $params->asArray());

    }

    /**
     * Create a standard params object that will be passed to the view
     * 
     * @param type $data
     * @param type $tableName
     * @param type $action
     * @return \Laravella\Crud\Params
     */
    private function __makeParams($data, $tableName, $action) {
        
        $tableMeta = Table::getTableMeta($tableName);
        
        $view = $this->__getView($tableName, $action);
        
        $pkTables = array();
        
        if (is_object($data)) {
            $data = $data->paginate($view->page_size);
            $pkTables = $this->__attachPkData($data, $tableMeta['fields_array']);
        }

        $prefix = array("id" => "/db/edit/$tableName/");


        $tableActionViews = $this->__getTableActionView($tableName, $view->id, $action);
        
        $selects = $this->__getPkSelects($tableMeta['fields_array']);
        
        return new Params($action, $tableMeta, $data, $tableActionViews, $pkTables, null, $prefix, $view, $selects);
        
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
 
        $params = $this->__makeParams(null, $tableName, $action);
        
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

        $tableMeta = $model->getMetaData($tableName);

        //get metadata as an array
        $metaA = $tableMeta['fields_array'];
        $meta = $tableMeta['fields'];
        $pkName = $tableMeta['table']['pk_name'];

        $table = DB::table($tableName)->where($pkName, '=', $pkValue)->get();

        $prefix = array();

        $data = Table::makeArray($meta, $table);

        $selects = $this->__getPkSelects($metaA);

        $view = $this->__getView($tableName, $action)->name;

        return View::make($view, array('action' => $action,
                    'data' => $data[0],
                    'meta' => $metaA,
                    'pkName' => $pkName,
                    'prefix' => $prefix,
                    'selects' => $selects,
                    'tableName' => $tableName));
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
    public function postInsert($tableName) {
        
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
                $arr[] = array('value' => $rec->$valueField, 'text' => $rec->$textField);
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
