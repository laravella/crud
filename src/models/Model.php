<?php use Laravella\Crud\DbGopher;
use Illuminate\Log;

/**
 * Description of generic Model
 *
 * @author Victor
 */
class Model extends Eloquent {  //why a Model and a meta.Table? Maybe extend meta.Table here

    protected $metaData = null;
    protected $primaryKey = "id";
    protected $guarded = array('id');

    /**
     * A way to overload the constructor. Use this to instantiate the class.
     * 
     * @param type $tableName
     */
    public static function getInstance()
    {
        $a = func_get_args(); 
        $i = func_num_args();
        
        $tmpInstance = new static;
        
        if (method_exists($tmpInstance, $f='getInstance'.$i)) { 
            return call_user_func_array(array($tmpInstance,$f),$a); 
        } 
    }

    /**
     * A way to overload the constructor. Use this to instantiate the class.
     * 
     * @param type $tableName
     */
    private static function getInstance1($tableName = null)
    {
        $model = new Model(array(), $tableName);
        return $model;
    }

    /**
     * A way to overload the constructor. Use this to instantiate the class.
     * 
     * @param type $tableName
     */
    private static function getInstance2($attributes, $tableName = null)
    {
        $model = new Model($attributes, $tableName);
        return $model;
    }

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @param  tableName  $tableName
     * 
     * @return void
     */
    function __construct() 
    { 
        $a = func_get_args(); 
        $i = func_num_args(); 
        if (method_exists($this,$f='__construct'.$i)) { 
            call_user_func_array(array($this,$f),$a); 
        } 
    } 

    private function __construct1(array $attributes = array()) 
    { 
        parent::__construct($attributes);
    } 
    
    private function __construct2(array $attributes = array(), $table = null) 
    { 
        $this->table = $table;
        $this->setTable($table);
        $this->setMetaData($table);
        $this->setGuarded(array($this->metaData['table']['pk_name']));
        parent::__construct($attributes);
    } 
        
    
    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable()
    {
        if (isset($this->table))
            return $this->table;

        return null;
    }

    /**
     * 
     * @param type $table
     * @param type $customKey
     * @return type
     */
    public function setHasOne($table, $customKey)
    {
        return $this->hasOne($table, $customKey);
    }

    /**
     * 
     * @param type $table
     * @param type $customKey
     * @return type
     */
    public function setHasMany($table, $customKey)
    {
        return $this->hasMany($table, $customKey);
    }

    /**
     * 
     * @param type $table
     * @param type $customKey
     * @return type
     */
    public function setBelongsTo($table, $customKey)
    {
        return $this->belongsTo($table, $customKey);
    }

    /**
     * 
     * @param type $table
     * @param type $pivotTable
     * @param type $remoteId
     * @param type $localId
     * @return type
     */
    public function setBelongsToMany($table, $pivotTable, $remoteId, $localId)
    {
        return $this->belongsToMany($table, $pivotTable, $remoteId, $localId);
    }

    public function setGuarded($guardedA)
    {
        $this->guarded = $guardedA;
    }

    public function getA()
    {
        
    }

    /**
     * Insert a new record
     */
    public function insertRec()
    {
        $fields = $this->metaData['fields_array'];

        $updateA = array();
        foreach ($fields as $field)
        {
            if ($this->isFillable($field['name']))
            {
                $updateA[$field['name']] = Input::get($field['name']);
            }
        }

        $id = DB::table($this->table)->insertGetId($updateA);

        return $id;
    }

    /**
     * Create an array of key values from http GET data
     * 
     * @param type $fields
     */
    private function __editGet($fields)
    {
        $updateA = array();
        foreach ($fields as $field)
        {
            if ($this->isFillable($field['name']))
            {
                $updateA[$field['name']] = Input::get($field['name']);
            }
        }
        return $updateA;
    }

    /**
     * Create an array of key values from http GET data
     * 
     * @param type $fields
     * @param type $json
     */
    private function __editJson($fields, $json)
    {
        die;

        $updateA = array();
        //print_r($json);
        $json = iconv("windows-1250", "UTF-8", $json);
        $input = json_decode($json);
        foreach ($fields as $field)
        {
            if ($this->isFillable($field['name']) && isset($input[$field['name']]))
            {
                $updateA[$field['name']] = $input[$field['name']];
            }
        }
        return $updateA;
    }

    /**
     * Update a record
     * 
     * @param type $pkValue
     * @return \Model
     */
    public function editRec($pkValue, $json = null)
    {

        $pkName = $this->metaData['table']['pk_name'];

        $fields = $this->metaData['fields_array'];

        if (empty($json))
        {
            $updateA = $this->__editGet($fields);
        }
        else
        {
            $updateA = $this->__editJson($fields, $json);
        }

        //print_r($updateA);
        DB::table($this->table)->where($pkName, '=', $pkValue)->update($updateA);

        return $this;
    }

    /**
     * 
     * 
     * @param type $userId
     * @return type
     */
    public static function getUserMenu ($userId = null) {
        if ($userId == null) {
            $userId = Auth::user()->id;
        }
        
        $menus = DB::table('users as u')->join('usergroups as ug', 'u.usergroup_id', '=', 'ug.id')
                ->join('_db_menu_permissions as mp', 'mp.usergroup_id', '=', 'ug.id')
                ->join('_db_menus as m', 'm.id', '=', 'mp.menu_id')
                ->join('_db_menus as m2', 'm2.parent_id', '=', 'm.id')
                ->where('u.id', '=', $userId)
                ->select('u.username', 'ug.group', 
                        'm.id', 'm.icon_class', 'm.label', 'm.href', 'm.parent_id', 
                        'm2.id as m2_id', 'm2.icon_class as m2_icon_class', 'm2.label as m2_label', 
                        'm2.href as m2_href', 'm2.parent_id as m2_parent_id')->get();

        $menuA = array();
        foreach($menus as $menu) {
            if (!isset($menuA[$menu->label])) {
                $menuA[$menu->label] = array();
            }
            $menuA[$menu->label][] = array('username'=>$menu->username, 'group'=>$menu->group, 
                'id'=>$menu->id, 'icon_class'=>$menu->icon_class, 'label'=>$menu->label, 
                'href'=>$menu->href, 'parent_id'=>$menu->parent_id, 'm2_id'=>$menu->m2_id, 
                'm2_icon_class'=>$menu->m2_icon_class, 'm2_label'=>$menu->m2_label, 
                'm2_href'=>$menu->m2_href, 'm2_parent_id'=>$menu->m2_parent_id);
        }
        return $menuA;
    }
    
    /**
     * Get assets linked to a page
     */
    public static function getAssets($page) {
        $assetsA = array();

        $assetType = "default";
        
        $pot = Options::get('skin').".dbview";
        
        if (isset($page) && is_object($page))
        {
            $pot = $page->view_name;
        }
        
        $assets = DB::table('_db_page_assets as pa')
        ->join('_db_option_types as pot', 'pot.id', '=', 'pa.page_type_id')
        ->join('_db_option_types as aot', 'aot.id', '=', 'pa.asset_type_id')
        ->join('_db_assets as a', 'a.asset_type_id', '=', 'pa.asset_type_id')
        ->join('_db_pages as p', 'p.page_type_id', '=', 'pa.page_type_id')
        ->join('_db_page_contents as pc', 'pc.page_id', '=', 'p.id')
        ->select('pa.id', 'pa.page_type_id', 'pa.asset_type_id', 'a.id', 
        'p.id', 'aot.name', 'pot.name', 'a.url', 'a.vendor', 'a.type', 'a.version', 
        'a.position', 'p.action_id', 'p.view_id', 'p.content_id', 'p.page_size', 
                'p.title', 'pc.slug')
        /*->where('pot.name', $pot)*/  //TODO : had to comment this out because upload isn't properly linked to assets and pages
        ->where('pc.slug', '_db_actions_getselect')
        ->get();

        foreach($assets as $asset) {
            $assetsA[] = array('url'=>$asset->type."/".$asset->url, 'type'=>$asset->type, 'position'=>$asset->position);
        }

        return $assetsA;
    }
    
    /**
     * Setter for table
     * 
     * @param type $tableName
     */
    public function setTable($tableName)
    {
        $this->table = $tableName;
    }

    /**
     * Setter for metaData
     * 
     * @param type $tableName
     */
    public function setMetaData($tableName)
    {
        $this->metaData = Table::getTableMeta($tableName);
    }

    /**
     * Getter for metaData
     * 
     * @return type
     */
    public function getMetaData()
    {
        return $this->metaData;
    }

    /**
     * Find the right view to use with the action
     * 
     * @param type $tableName
     * @param type $action
     */
    public static function getViewData($tableName, $action)
    {
        $views = DB::table('_db_pages')
                ->join('_db_tables', '_db_pages.table_id', '=', '_db_tables.id')
                ->join('_db_actions', '_db_pages.action_id', '=', '_db_actions.id')
                ->join('_db_views', '_db_pages.view_id', '=', '_db_views.id')
                ->where('_db_actions.name', '=', $action)
                ->where('_db_tables.name', '=', $tableName)
                ->select('_db_views.id', '_db_views.name', '_db_pages.page_size')
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
    public static function getPageData($tableName = null, $view = null, $action = null, $contentId = null)
    {
        $viewId = null;

        if (empty($view)) { 
            
            if (!empty($tableName) && !empty($action)) {
                $view = Model::getViewData($tableName, $action);
            }
            $viewId = DbGopher::coalesce($view, 'id');
        } else {
            if(!is_numeric($view))
            {
                $viewO = DB::table('_db_views')->where('name', $view)->first();
                $viewId = $viewO->id;
            }
        }
        
        $tavO = DB::table('_db_pages')
                ->join('_db_tables', '_db_pages.table_id', '=', '_db_tables.id')
                ->join('_db_actions', '_db_pages.action_id', '=', '_db_actions.id')
                ->join('_db_views', '_db_pages.view_id', '=', '_db_views.id')
                ->select('_db_pages.view_id', '_db_pages.id as page_id', '_db_pages.action_id', 
                        '_db_pages.content_id', '_db_pages.slug',
                        '_db_views.name as view_name', '_db_actions.name as action_name', 
                        '_db_tables.name as table_name', '_db_pages.title');
        if (isset($viewId) && !empty($viewId))
        {
            $tavO = $tavO->where('_db_pages.view_id', '=', $viewId);
        }
        if (isset($action) && !empty($action))
        {
            $tavO = $tavO->where('_db_actions.name', '=', $action);
        }
        if (isset($tableName) && !empty($tableName))
        {
            $tavO = $tavO->where('_db_tables.name', '=', $tableName);
        }
        if (isset($contentId) && !empty($contentId))
        {
            $tavO = $tavO->where('_db_pages.content_id', '=', $contentId);
        }
        $tva = $tavO->first();
        
//        p(DbGopher::getLastQuery());
//        die;
        
        return $tva;
    }
    
    /**
     * Find the right object and view to use with the page slug
     * 
     * @param type $tableName
     * @param type $action
     */
    public static function getSlug($slug)
    {
        $views = DB::table('_db_pages')
                ->join('_db_objects', '_db_pages.object_id', '=', '_db_objects.id')
                ->join('_db_views', '_db_pages.view_id', '=', '_db_views.id')
                ->where('_db_pages.slug', '=', $slug)
                ->first();
        return $views;
    }
    
    /**
     * Loop through foreign keys and generate an array of select boxes for each
     * related primary key
     * 
     * @param type $meta
     */
    public static function getPkSelects($meta)
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
                if (isset($metaField['pk_display']) && !empty($metaField['pk_display']))
                {
                    $pkd = $metaField['pk_display'];
                }

                //get an array of key-value pairs to be used as select boxes in the front end
                $selectA[$metaField['name']] = self::getSelectBox($pk['tableName'], $pk['name'], $pkd['name']);
            }
        }
        return $selectA;
    }

    /**
     * Get a select array(object(value, text))
     */
    public static function getSelectBox($table, $valueField, $textField)
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
    
}

?>
