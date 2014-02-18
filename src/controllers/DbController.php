<?php

use Laravella\Crud\Params;
use Laravella\Crud\DbGopher;
use Laravella\Crud\Options;
use Laravella\Crud\CrudSeeder;

/**
 * All database requests are handled by this controller, 
 * even the DbApiController ones, although DbApiController is leaner on the response i.e. json.
 */
class DbController extends AuthorizedController {

    private $layoutName = '.default';
    private $viewName = '.dbview';
    private $skinType = 'admin'; //admin, front, (later : upload ... etc.)
    //protected $layout = //getLayout;
    private $log = array();

    const SUCCESS = "success";
    const INFO = "info";
    const IMPORTANT = "important";
    const HTML = "text/html";
    const XML = "text/xml";
    const JSON = "text/json";

    private $model = null;
    public $displayType = self::HTML;
    public $params = null;

    public function getSkin()
    {
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
    public function getLayout($type = 'admin')
    {
//        return Options::get('skin', $type) . $this->layoutName;
        $skin = Options::getSkin();
        return $viewName = $skin[$type] . $this->layoutName;
    }

    /**
     * Does a $haystack containt a $needle?
     * 
     * @param String $haystack
     * @param String $needle
     * @return boolean
     */
    public static function contains($haystack, $needle)
    {
        $pos = strpos($haystack, $needle);

        if ($pos === false)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    /**
     * Add or update keys
     */
    public function postKeys()
    {
        $input = Input::all();
        $name = '';
        $keyFields = array();
        
        foreach ($input as $field => $value)
        {
            $parts = explode(":", $field);
            
            if ($field == 'key_name')
            {
                $name = $value;
            }
            else if (static::contains($field, 'order'))
            {
                $kfId = $parts[1];
                $keyFields[$kfId]['order'] = $value;
            }
            else if (static::contains($field, 'pkfi_fid'))
            {
                $kfId = $parts[1];
                $keyFields[$kfId]['pkfi_fid'] = $value;
            }
            else if (static::contains($field, 'pkfn_fid'))
            {
                $kfId = $parts[1];
                $keyFields[$kfId]['pkfn_fid'] = $value;
            }
            else if (static::contains($field, 'fkfi_fid'))
            {
                $kfId = $parts[1];
                $keyFields[$kfId]['fkfi_fid'] = $value;
            }
            else if (static::contains($field, 'fkfn_fid'))
            {
                $kfId = $parts[1];
                $keyFields[$kfId]['fkfn_fid'] = $value;
            }
            
        }
        var_dump($keyFields);
        die;
    }

    /**
     * Display a form to edit keys between tables
     * 
     * @return type
     */
    public function getKeys($id = null)
    {
        $action = '';
        $selects = array();

        if (isset($id) && !empty($id))
        {
            $keys = DB::table('_db_key_fields as kf')
                    ->join('_db_keys as k', 'kf.key_id', '=', 'k.id')
                    ->join('_db_key_types as kt', 'kf.key_type_id', '=', 'kt.id')
                    ->join('_db_fields as pkfi', 'kf.pk_field_id', '=', 'pkfi.id')
                    ->join('_db_fields as pkfn', 'kf.pk_display_field_id', '=', 'pkfn.id')
                    ->join('_db_fields as fkfi', 'kf.fk_field_id', '=', 'fkfi.id')
                    ->join('_db_fields as fkfn', 'kf.fk_display_field_id', '=', 'fkfn.id')
                    ->where('k.id', '=', $id)
                    ->select('k.id as key_id', 'k.name as key_name', 'pkfi.id as pkfi_fid', 'pkfi.fullname as pkfi_fin', 'pkfn.id as pkfn_fid', 'pkfn.fullname as pkfn_fin', 'fkfi.id as fkfi_fid', 'fkfi.fullname as fkfi_fin', 'fkfn.id as fkfn_fid', 'fkfn.fullname as fkfn_fin', 'kf.id as key_field_id', 'kf.order', 'kf.key_type_id')
                    ->get();

            $selects['fullname'] = Model::getSelectBox('_db_fields', 'id', 'fullname');
            $selects['key_type_id'] = Model::getSelectBox('_db_key_types', 'id', 'name');

            $action = "getKeyEdit";
        }
        else
        {
            $keys = DB::table('_db_key_fields as kf')
                    ->join('_db_keys as k', 'kf.key_id', '=', 'k.id')
                    ->join('_db_key_types as kt', 'kf.key_type_id', '=', 'kt.id')
                    ->join('_db_fields as pkfi', 'kf.pk_field_id', '=', 'pkfi.id')
                    ->join('_db_fields as pkfn', 'kf.pk_display_field_id', '=', 'pkfn.id')
                    ->join('_db_fields as fkfi', 'kf.fk_field_id', '=', 'fkfi.id')
                    ->join('_db_fields as fkfn', 'kf.fk_display_field_id', '=', 'fkfn.id')
                    ->select('k.id as key_id', 'k.name as key_name', 'pkfi.id as pkfi_fid', 
                            'pkfi.fullname as pkfi_fin', 'pkfn.id as pkfn_fid', 'pkfn.fullname as pkfn_fin', 
                            'fkfi.id as fkfi_fid', 'fkfi.fullname as fkfi_fin', 'fkfn.id as fkfn_fid', 
                            'fkfn.fullname as fkfn_fin', 'kf.id as key_field_id', 'kf.order', 'kf.key_type_id')
                    ->get();
            $action = "getKeys";
        }

        $kA = array();
        foreach ($keys as $n => $k)
        {
            $kA[] = array(
                'pkfi_fid' => $k->pkfi_fid, 'pkfn_fid' => $k->pkfn_fid,
                'fkfi_fid' => $k->fkfi_fid, 'fkfn_fid' => $k->fkfn_fid,
                'key_id' => $k->key_id, 'key_name' => $k->key_name,
                'key_field_id' => $k->key_field_id,
                'order' => $k->order, 'key_type_id' => $k->key_type_id);
        }

        $this->params = new Params('_db_tables', 'getSelect', 'admin');
        $paramsA = $this->params->asArray();
        $paramsA['dataA'] = $kA;
        $paramsA['action'] = $action;
        $paramsA['selects'] = $selects;

        if (!isset($paramsA['layout']) || empty($paramsA['layout']))
        {
            $skinType = $paramsA['frontend'] ? 'frontend' : 'admin';
            $paramsA['layout'] = $this->getLayout($skinType);
        }

        $skin = Options::getSkin();
        $viewName = Options::getSkinName($skin, 'admin', 'dbview');

        return View::make($paramsA['layout'])->nest('content', $viewName, $paramsA);
    }

    /**
     * 
     * @param type $type
     * @return type
     */
    public function getView($type = 'admin')
    {
        $skin = Options::getSkin();
        return $viewName = $skin[$type] . $this->viewName;
//        return Options::get('skin', $type) . $this->viewName;
    }

    /**
     * Get the content slug which corresponds to contents.slug
     * 
     * @param type $contentSlug
     * @return type
     */
    public function getIndex($contentSlug = 'contents_getpage')
    {
        $this->params = Params::bySlug(true, $contentSlug, $this->displayType, $this->getView('frontend'));
        return $this->makeView();
    }

    /**
     * The root of the crud application /db
     * 
     * @return type
     */
    public function getAdmin()
    {
        return $this->getPage();
    }

    /**
     * 
     * @param type $table
     */
    protected function getPage($table = 'contents', $action = 'getPage', $frontend = false)
    {
        //get related data
        $this->params = new Params($table, $action, $this->displayType, DB::table($table), $frontend);
        return $this->makeView();
    }

    /**
     * 
     * @param type $paramsArray Params->asArray()
     * @return type
     */
    public function makeView()
    {

        $paramsA = $this->params->asArray();

        $skinType = $paramsA['frontend'] ? 'frontend' : 'admin';
        $paramsA['view'] = $this->getView($skinType);

        if (!isset($paramsA['view']) || empty($paramsA['view']))
        {
            $skinType = $paramsA['frontend'] ? 'frontend' : 'admin';
            $paramsA['view'] = $this->getView($skinType);
        }
        if (!isset($paramsA['layout']) || empty($paramsA['layout']))
        {
            $skinType = $paramsA['frontend'] ? 'frontend' : 'admin';
            $paramsA['layout'] = $this->getLayout($skinType);
        }

        return View::make($paramsA['layout'])->nest('content', $paramsA['view'], $paramsA);
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
    public function getSelect($tableName = null)
    {
        return $this->getPage($tableName, 'getSelect');
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

        $searchObj = json_decode($q, true);
        foreach ($searchObj as $sTable => $sFields)
        {
            $table = DB::table($sTable);
            foreach ($sFields as $sField => $sValue)
            {
                $table->where($sField, '=', $sValue);
            }
        }

        $this->params = new Params($tableName, $action, $this->displayType, $table);

        return $this->makeView();
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
        try
        {
            DB::table($tableName)->where('id', '=', $recorid)->delete();
        }
        catch (Exception $e)
        {
            
        }
        $this->params = new Params($tableName, $action, $this->displayType, null);
        $res = '{"status":"failed"}';
        if (is_object($this->params))
        {
            $res = json_encode($this->params->asArray());
        }
        return $res;
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

        $this->params = new Params($tableName, $action, $this->displayType, null);

        return $this->makeView();
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

        $this->params = new Params($tableName, $action, $this->displayType, $table);

        return $this->makeView();
    }

    /**
     * Alter Meta data
     */
    public function getAlter()
    {
        $ts = new TableSeeder();
        $ts->addTable('tableName', array(), array());
        return "hello";
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
     * This is for custom front-end actions that simply needs the Params object 
     * and defines it's own view via the table_action_view table
     * 
     * @param type $parameters
     * @return type
     */
    protected function _customAction($parameters)
    {

        if (!empty($parameters) && is_array($parameters))
        {
            $action = 'get' . $parameters[0];
            if (count($parameters) > 1)
            {
                $tableName = $parameters[1];
                $tableMeta = Table::getTableMeta($tableName);

                //get metadata as an array
                $pkName = $tableMeta['table']['pk_name'];
                $pkName = ($tableName == 'contents') ? 'slug' : 'id';

                $table = null;

                if (isset($parameters[2]))
                {
                    $table = DB::table($tableName)->where($pkName, '=', $parameters[2]);
                }
                else if ($action == 'getRegister')
                {
                    $table = array();
                }
                else
                {
                    $table = DB::table($tableName);
                }

                $this->params = new Params($tableName, $action, $this->displayType, $table);

                $this->makeView();
            }
        }
        else
        {
            return View::make(Options::get('skin') . '.default');
        }
    }

    public function getTest()
    {
        
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
