<?php

use Laravella\Crud\Params;
use Laravella\Crud\DbGopher;

/**
 * This is used for ajax calls. 
 * It just formats the data differently but the work is still done by DbController class.
 */
class KeysController extends DbController {
    
//    private $layoutName = '.content';
//    private $viewName = '.dbview';
//    
    public $displayType = self::HTML; //or self::JSON or self::HTML

    /**
     * Add or update keys
     */
    public function postKeys()
    {
        
        $input = Input::all();
        
        /*
        echo "asdf";
        echo var_dump($input);
        die;
        */
        
        $name = '';
        $keyFields = array();
        $keys = array();
        
        foreach ($input as $field => $value)
        {
            $parts = explode(":", $field);
            
            if ($field == 'key_id')
            {
                $keys['id'] = $value;
            } 
            else if ($field == 'key_name')
            {
                $keys['name'] = $value;
            }
            else if (static::contains($field, 'order'))
            {
                $kfId = $parts[1];
                $keyFields[$kfId]['order'] = $value;
            }
            else if (static::contains($field, 'pkfi_fid'))
            {
                $kfId = $parts[1];
                $keyFields[$kfId]['pk_field_id'] = $value;
            }
            else if (static::contains($field, 'pkfn_fid'))
            {
                $kfId = $parts[1];
                $keyFields[$kfId]['pk_display_field_id'] = $value;
            }
            else if (static::contains($field, 'fkfi_fid'))
            {
                $kfId = $parts[1];
                $keyFields[$kfId]['fk_field_id'] = $value;
            }
            else if (static::contains($field, 'fkfn_fid'))
            {
                $kfId = $parts[1];
                $keyFields[$kfId]['fk_display_field_id'] = $value;
            }
        }
        
        $cs = new CrudSeeder();

        //var_dump($keys);
        foreach($keyFields as $id=>$key) {
            $key['id'] = $id;
            
            $cs->updateOrInsert('_db_key_fields', array("id"=>$key['id']), $key);
            $cs->updateOrInsert('_db_keys', array("id"=>$keys['id']), $keys);
        }
        
        return $this->getKeys();
        
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
            $keys = DB::table('_db_keys as k')
                    ->leftJoin('_db_key_fields as kf', 'kf.key_id', '=', 'k.id')
                    ->leftJoin('_db_key_types as kt', 'kf.key_type_id', '=', 'kt.id')
                    ->leftJoin('_db_fields as pkfi', 'kf.pk_field_id', '=', 'pkfi.id')
                    ->leftJoin('_db_fields as pkfn', 'kf.pk_display_field_id', '=', 'pkfn.id')
                    ->leftJoin('_db_fields as fkfi', 'kf.fk_field_id', '=', 'fkfi.id')
                    ->leftJoin('_db_fields as fkfn', 'kf.fk_display_field_id', '=', 'fkfn.id')
                    ->where('k.id', '=', $id)
                    ->select('k.id as key_id', 'k.name as key_name', 'pkfi.id as pkfi_fid', 
                            'pkfi.fullname as pkfi_fin', 'pkfn.id as pkfn_fid', 'pkfn.fullname as pkfn_fin', 
                            'fkfi.id as fkfi_fid', 'fkfi.fullname as fkfi_fin', 'fkfn.id as fkfn_fid', 
                            'fkfn.fullname as fkfn_fin', 'kf.id as key_field_id', 'kf.order', 'kf.key_type_id')
                    ->get();

            $selects['fullname'] = Model::getSelectBox('_db_fields', 'id', 'fullname');
            $selects['key_type_id'] = Model::getSelectBox('_db_key_types', 'id', 'name');

            $action = "getKeyEdit";
        }
        else
        {
            $keys = DB::table('_db_key_fields as kf')
                    ->leftJoin('_db_keys as k', 'kf.key_id', '=', 'k.id')
                    ->leftJoin('_db_key_types as kt', 'kf.key_type_id', '=', 'kt.id')
                    ->leftJoin('_db_fields as pkfi', 'kf.pk_field_id', '=', 'pkfi.id')
                    ->leftJoin('_db_fields as pkfn', 'kf.pk_display_field_id', '=', 'pkfn.id')
                    ->leftJoin('_db_fields as fkfi', 'kf.fk_field_id', '=', 'fkfi.id')
                    ->leftJoin('_db_fields as fkfn', 'kf.fk_display_field_id', '=', 'fkfn.id')
                    ->select('k.id as key_id', 'k.name as key_name', 'pkfi.id as pkfi_fid', 
                            'pkfi.fullname as pkfi_fin', 'pkfn.id as pkfn_fid', 'pkfn.fullname as pkfn_fin', 
                            'fkfi.id as fkfi_fid', 'fkfi.fullname as fkfi_fin', 'fkfn.id as fkfn_fid', 
                            'fkfn.fullname as fkfn_fin', 'kf.id as key_field_id', 'kf.order', 'kf.key_type_id')
                    ->get();
            $action = "getKeys";
        }

        $this->setParams($keys, $selects, $action);
        
        return $this->makeView('.default', '.dbview');
    }

    /**
     * Attach key details to params
     * 
     * @param type $keys
     * @return type
     */
    public function setParams($keys, $selects, $action, $displayType = self::HTML) {

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

        $this->params = new Params('_db_keys', $action, $displayType);
        $this->params->dataA = $kA;
        $this->params->tableName = '_db_keys';
        $this->params->action = $action;
        $this->params->title = 'Keys';
        $this->params->selects = $selects;
    }

    /**
     * Return a detail row
     * 
     * @param type $id
     */
    public function getField($parentId = null, $id = null) {
        $this->getData('getRow', $parentId, $id);
        return $this->makeView('.apilayout', '.apiview');
    }
    
    /**
     * 
     */
    public function postKeyfield($parentId = null, $id = null) {
        $this->getData('getRow', $parentId, $id);
        return $this->makeView('.apilayout', '.apiview');
    }
    
    /**
     * 
     * @param type $id
     * @param type $viewName
     * @param type $layoutName
     */
    public function getData($action, $parentId = null, $id = null) {
        $selects = array();
        $keys = array();

        if (!isset($id) || is_null($id) || empty($id) || !is_int($id))
        {
            $id = DB::table('_db_key_fields')->insertGetId(array('key_id'=>$parentId));
        }
        
        $keys = DB::table('_db_key_fields as kf')
            ->leftJoin('_db_keys as k', 'kf.key_id', '=', 'k.id')
            ->leftJoin('_db_key_types as kt', 'kf.key_type_id', '=', 'kt.id')
            ->leftJoin('_db_fields as pkfi', 'kf.pk_field_id', '=', 'pkfi.id')
            ->leftJoin('_db_fields as pkfn', 'kf.pk_display_field_id', '=', 'pkfn.id')
            ->leftJoin('_db_fields as fkfi', 'kf.fk_field_id', '=', 'fkfi.id')
            ->leftJoin('_db_fields as fkfn', 'kf.fk_display_field_id', '=', 'fkfn.id')
            ->where('kf.id', '=', $id)
            ->select('k.id as key_id', 'k.name as key_name', 'pkfi.id as pkfi_fid', 'pkfi.fullname as pkfi_fin', 'pkfn.id as pkfn_fid', 'pkfn.fullname as pkfn_fin', 'fkfi.id as fkfi_fid', 'fkfi.fullname as fkfi_fin', 'fkfn.id as fkfn_fid', 'fkfn.fullname as fkfn_fin', 'kf.id as key_field_id', 'kf.order', 'kf.key_type_id')
            ->get();
            
        $selects['fullname'] = Model::getSelectBox('_db_fields', 'id', 'fullname');
        $selects['key_type_id'] = Model::getSelectBox('_db_key_types', 'id', 'name');
        $this->setParams($keys, $selects, $action);
    }
    
}

?>
