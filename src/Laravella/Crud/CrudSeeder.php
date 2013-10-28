<?php

namespace Laravella\Crud;

use Laravella\Crud\Log;
use \Seeder;
use \Model;
use \DB;
use \Hash;

/**
 * Basic functions to manipulate meta data
 */
class CrudSeeder extends Seeder {

    private $idCache = array();
    private $pkTypeId = null;
    private $fkTypeId = null;

    /**
     * Set the title of a page
     */
    public function setTitle($slug, $title)
    {
        $recs = DB::table('_db_pages')
                ->where('slug', $slug)
                ->update(array('title' => $title));
        return $recs;
    }

    /**
     * Set the Caption of an image (_db_medias.caption)
     */
    public function setHeroCaption($mediaId, $caption)
    {
        
    }

    /*
     * Set the page's type
     */
    public function setPageType() {
        
    }
    
    /*
     * Set the asset's type
     */
    public function setAssetType() {
        
    }
    
    /**
     * 
     * @param type $id
     * @param type $slugs
     */
    public function linkAssetPageGroups($assetGroup, $pageGroup){
        
    }
    
    
    /**
     * 
     * @param type $tableName The name of the table 
     * @param type $actionName The name of the action
     * @param type $viewName The name of the view
     */
    public function tableActionViewId($tableName, $actionName, $viewName)
    {
        $tableId = $this->getId('_db_tables', 'name', $tableName);
        $actionId = $this->getId('_db_actions', 'name', $actionName);
        $viewId = $this->getId('_db_views', 'name', $viewName);

        $recs = DB::table('_db_pages')->where('table_id', $tableId)
                ->where('action_id', $actionId)
                ->where('view_id', $viewId);
        return $recs;
    }

    /**
     * 
     * @param type $tableName
     * @param type $actionName
     * @param type $viewName
     * @param type $values
     * @return type
     */
    public function addPage($tableName, $actionName, $viewName, $values)
    {
        return $this->tableActionView($tableName, $actionName, $viewName, $values);
    }

    /**
     * Deprecated. Use addPage.
     * 
     * @param type $tableName
     * @param type $actionName
     * @param type $viewName
     * @param array $values
     * 
     * @deprecated 
     */
    public function tableActionView($tableName, $actionName, $viewName, $values)
    {
        $tableId = $this->getId('_db_tables', 'name', $tableName);
        $actionId = $this->getId('_db_actions', 'name', $actionName);
        $slug = strtolower($tableName . '_' . $actionName);
        $values['slug'] = $slug;
        $id = null;
        if (!is_null($viewName) && !empty($viewName))
        {
            $viewId = $this->getId('_db_views', 'name', $viewName);
            $id = $this->updateOrInsert('_db_pages', array('table_id' => $tableId, 'action_id' => $actionId, 'view_id' => $viewId), $values);
        }
        else
        {
            $id = $this->updateOrInsert('_db_pages', array('table_id' => $tableId, 'action_id' => $actionId), $values);
        }
        return $id;
    }

    /**
     * 
     * @param type $url
     * @param type $type
     * @param type $vendor
     * @param type $version
     */
    public function addAsset($url, $type = '', $assetGroup='', $vendor = '', $version = '')
    {
        $optionTypes = $this->getOptionType($assetGroup);
        $assetTypeID = $optionTypes[0]['id'];
        $values = array('url' => $url, 'type' => $type, 'asset_type_id' => $assetTypeID, 'vendor' => $vendor, 'version' => $version);
        $id = $this->updateOrInsert('_db_assets', array('url' => $url), $values);
        return $id;
    }

    /**
     * 
     * @param type $id
     * @param type $slugs Use '*' for all pages
     */
    public function linkAssetPage($id, $slugs)
    {
        $pages = array();
        if (!is_array($slugs))
        {
            if ($slugs == '*')
            {
                //all slugs
                $this->info("linking asset id $id with *");
                $pageTypes = DB::table('_db_option_types as ot1')
                        ->join('_db_option_types as ot2', 'ot1.parent_id', '=', 'ot2.id')
                        ->where('ot2.name', 'pages')->get();
                foreach ($pageTypes as $pageType)
                {
                    $this->updateOrInsert('_db_page_assets', array('asset_type_id' => $id, 'page_type_id' => $pageType->id));
                }
            }
            else
            {
                //specific slug
                $this->info("linking asset id $id with $slugs");
                $pageTypes = DB::table('_db_option_types as ot1')
                        ->join('_db_option_types as ot2', 'ot1.parent_id', '=', 'ot2.id')
                        ->where('ot2.name', 'pages')
                        ->where('ot1.name', $slugs)
                        ->get();
                foreach ($pageTypes as $pageType)
                {
                    $this->updateOrInsert('_db_page_assets', array('asset_type_id' => $id, 'page_id' => $pageType->id));
                }
            }
        }
        else
        {
            foreach ($slugs as $slug)
            {
                //an array of slugs
                $pageTypes = DB::table('_db_option_types as ot1')
                        ->join('_db_option_types as ot2', 'ot1.parent_id', '=', 'ot2.id')
                        ->where('ot2.name', 'pages')
                        ->where('ot1.name', $slug)
                        ->get();
                $pages = DB::table('_db_pages')->where('slug', $slug)->get();
                foreach ($pages as $pageType)
                {
                    $this->info("linking asset id $id with $pageType");
                    $this->updateOrInsert('_db_page_assets', array('asset_type_id' => $id, 'page_id' => $pageType->id));
                }
            }
        }
    }

    public function info($message)
    {
        Log::write(Log::INFO, $message);
    }

    /**
     * Update a reference to primary keys in _db_fields
     * 
     * @param type $fkTableName
     * @param type $fkFieldName
     * @param type $pkTableName
     * @param type $pkFieldName
     */
    public function updateReference($fkTableName, $fkFieldName, $pkTableName, $pkFieldName, $pkDisplayFieldName)
    {
        //get the id of the pkTableName in _db_tables
        $fkTableId = DB::table('_db_tables')->where('name', $fkTableName)->pluck('id');

        $pkTableId = DB::table('_db_tables')->where('name', $pkTableName)->pluck('id');

        if (!isset($this->pkTypeId))
        {
            $this->pkTypeId = DB::table('_db_key_types')->where('name', 'primary')->pluck('id');
        }

        if (!isset($this->fkTypeId))
        {
            $this->fkTypeId = DB::table('_db_key_types')->where('name', 'foreign')->pluck('id');
        }

        //get the id of the primary key field in _db_fields
        //for each field in the _db_fields table there will thus be a reference to 
        $pkFieldId = DB::table('_db_fields')
                ->where('table_id', $pkTableId)
                ->where('name', $pkFieldName)
                ->pluck('id');

        $pkDisplayFieldId = DB::table('_db_fields')
                ->where('table_id', $pkTableId)
                ->where('name', $pkDisplayFieldName)
                ->pluck('id');

        $fkFieldId = DB::table('_db_fields')
                ->where('table_id', $fkTableId)
                ->where('name', $fkFieldName)
                ->pluck('id');

        $message = "inserting into _db_fields : where " .
                "pkTableName = $pkTableName, " .
                "pkFieldName = $pkFieldName, " .
                "pkTableId = $pkTableId, " .
                "pkFieldId = $pkFieldId, " .
                "fkTableName = $fkTableName, " .
                "fkFieldName = $fkFieldName, " .
                "fkTableId = $fkTableId, " .
                "fkFieldId = $fkFieldId";

        Log::write("success", $message);

//KEEP THIS
//set the reference on the fk field
        /*
          DB::table('_db_fields')
          ->where('table_id', $fkTableId)
          ->where('name', $fkFieldName)
          ->update(array('pk_field_id' => $pkFieldId, 'pk_display_field_id' => $pkDisplayFieldId));
         */

        if (empty($fkFieldId) || empty($pkFieldId))
        {
            echo "\n There was an error finding the keys from the database. \n
                You might have a problem in Laravella\Crud\UpdateReferences \n
                or another class that extends Laravella\Crud\CrudSeeder and calls updateReference() \n";
            echo $message . "\n";
            die;
        }

        $keyName = "{$pkTableName}.{$pkFieldName} - {$fkTableName}.{$fkFieldName}";

        $keyId = DB::table('_db_keys')->insertGetId(array('name' => $keyName));

        $insertValues = array(
            'order' => 0,
            'key_id' => $keyId,
            'pk_field_id' => $pkFieldId,
            'pk_display_field_id' => $pkDisplayFieldId,
            'fk_field_id' => $fkFieldId,
            'fk_display_field_id' => $fkFieldId,
            'key_type_id' => $this->pkTypeId);

        $this->updateOrInsert('_db_key_fields', $insertValues, $insertValues);

//        DB::table('_db_key_fields')->insert($insertValues);

        /*
          $this->__log("success", "updating record : {$fkRec->id}");

          DB::table('_db_fields')
          ->where('table_id', $fkTableId)
          ->where('name', $fkFieldName)
          ->update(array('pk_field_id' => $fieldId));
         */
    }

    /**
     * Get the id of a record based on the value of another field
     * 
     * @param type $whereField
     * @param type $whereValue
     */
    public function getId($table, $whereField, $whereValue = null)
    {
        $key = $table . ':' . $whereField . ':' . $whereValue;
        if (!isset($this->idCache[$key]))
        {
            $m = Model::getInstance(array(), $table);
            $query = $table . ' ';
            if (is_array($whereField))
            {
                //$whereField is an array of key-value pairs
                foreach ($whereField as $key => $value)
                {
                    $m = $m->where($key, $value);
                    $query .= $key . '=\'' . $value . '\' ';
                }
            }
            else
            {
                $m = $m->where($whereField, $whereValue);
                $query .= $whereField . '=\'' . $whereValue . '\' ';
            }
            $recs = $m->get();
            if (count($recs) != 1)
            {
//                throw new DBException(count($recs) . ' Unique id not found where ' . $query);
                return null;
            }
            else
            {
                $idCache[$key] = $recs[0]->id;
            }
        }
        return $idCache[$key];
    }

    /**
     * Add a severity
     * 
     * @param type $severity
     */
    public function addSeverity($severity)
    {
        $arr = array("name" => $severity);
        $viewId = DB::table('_db_severities')->insertGetId($arr);
        Log::write("success", " - $severity severity inserted");
    }

    public function makeApiKey()
    {
        $password = rand(23450987, 234509870);
        $password = md5($password);
        return $password;
    }

    /**
     * Add a new user
     * 
     * @param type $groupName
     * @param type $name
     * @param type $password
     * @param type $email
     * @param type $firstName
     * @param type $lastName
     * @return type
     */
    public function createUser($groupName, $name, $password, $email, $firstName, $lastName)
    {

        $this->command->info($groupName . ' ' . $name);

        Log::write(Log::INFO, '[' . $groupName . '] ' . $name);

        $hashPass = Hash::make($password);

        //$group = DB::table('groups')->where('name', $groupName)->first();
        $userGroup = DB::table('usergroups')->where('group', $groupName)->first();

        $adminUser = array('username' => $name, 'password' => $hashPass, 'email' => $email, 'first_name' => $firstName, 'last_name' => $lastName); //Config::get('crud::app.setup_user');
        $adminUser['activated'] = true;
        $adminUser['api_token'] = $this->makeApiKey();
        if (is_object($userGroup))
        {
            $adminUser['usergroup_id'] = $userGroup->id;
            $this->command->info('usergroup id is : ' . $userGroup->id);
        }

        $userId = DB::table('users')->insertGetId($adminUser);

        if (is_object($userGroup))
        {
//            DB::table('users_groups')->insert(array('user_id' => $userId, 'usergroup_id' => $userGroup->id));
        }

        return $userId;
    }

    /**
     * 
     * Add an option
     * 
     * @param type $optionTypeId
     * @param type $name
     * @param type $value
     * @return type
     */
    public function addOption($optionTypeId, $name, $value)
    {
        $option = array('option_type_id' => $optionTypeId, 'name' => $name, 'value' => $value);
        $optionId = DB::table('_db_options')->insertGetId($option);
        Log::write(Log::INFO, $name . ' option created');
        return $optionId;
    }

    /**
     * Add an option type 
     * 
     * @param type $name
     * @param type $parentId
     * @return type
     */
    public function addOptionType($name, $parentId = null)
    {
        // an option with name "grand.parent.name"
//        if (is_null($parentId)) {
//            $parentA = explode('.', $name);
//            foreach($parentA as $parent) {
//                $optionTypes = DB::table('_db_option_types as ot1')
//                        ->join('_db_option_types as ot2', 'ot1.id', '=', 'ot2.parent_id')
//                        ->where('ot1.name', $parentName)
//                        ->where('ot2.name', $name)
//                        ->select('ot1.name', 'ot1.id')
//                        ->get();
//            }
//        }
        $optionType = array('name' => $name, 'parent_id' => $parentId);
        $optionTypeId = DB::table('_db_option_types')->insertGetId($optionType);
        Log::write(Log::INFO, $name . ' option type created');
        return $optionTypeId;
    }

    /**
     * Add an option type 
     * 
     * @param type $name
     * @param type $parentId
     * @return type
     */
    public function getOptionType($name, $parentName = null)
    {
        $optionTypeA = array();
        $optionTypes = null;
        if (!is_null($parentName) && !empty($parentName))
        {
            $optionTypes = DB::table('_db_option_types as ot1')
                    ->join('_db_option_types as ot2', 'ot1.id', '=', 'ot2.parent_id')
                    ->where('ot1.name', $parentName)
                    ->where('ot2.name', $name)
                    ->select('ot1.name', 'ot1.id')
                    ->get();
        }
        else
        {
            $optionTypes = DB::table('_db_option_types as ot1')
                    ->where('ot1.name', $name)
                    ->select('ot1.name', 'ot1.id')
                    ->get();
        }
        foreach($optionTypes as $optionType) 
        {
            $optionTypeA[] = array('id' => $optionType->id, 'name' => $optionType->name);
        }
        return $optionTypeA;
    }

    /**
     * Add an asset type
     * 
     * @param type $typeName
     * @return type
     */
    public function addAssetType($typeName)
    {
        $crudId = $this->addOptionType('crud');
        $assetsId = $this->addOptionType('assets', $crudId);
        $assetTypeId = $this->addOptionType($typeName, $assetsId);
        return $assetTypeId;
    }

    /**
     * 
     * @param type $pageType
     * @return type
     */
    public function addPageType($pageType) {
        $crudId = $this->addOptionType('crud');
        $pagesId = $this->addOptionType('pages', $crudId);
        $pageTypeId = $this->addOptionType($pageType, $pagesId);
        return $pageTypeId;
    }
    
    /**
     * Add a new usergroup
     * 
     * @param type $groupName
     */
    public function addGroup($groupName)
    {
        $group = array('name' => $groupName);     //can change permissions
        DB::table('groups')->insert($group);
        Log::write('info', $groupName . ' usergroup created');
    }

    /**
     * Updates a field or inserts a record if key does not exist
     * 
     * @param type $updateTable
     * @param type $whereValues an array of key value pairs , or an id
     */
    public function delete($updateTable, $whereValues)
    {
        $m = Model::getInstance($updateTable);
        if (is_array($whereValues))
        {
            //$whereField is an array of key-value pairs
            foreach ($whereValues as $key => $value)
            {
                $m = $m->where($key, $value);
            }
        }
        else
        {
            $m = $m->where('id', $whereValues);
        }
        $m->delete();
    }

    /**
     * Updates a field or inserts a record if key does not exist
     * 
     * @param type $updateTable The table that is to be updated
     * @param type $whereValues an array of key value pairs , or an id
     * @param type $insertValues Used as a single value if whereField is a string, else excluded
     */
    public function updateOrInsert($updateTable, $whereValues, array $insertValues = null)
    {

        if (is_null($insertValues))
        {
            $insertValues = $whereValues;
        }

        $m = Model::getInstance($updateTable);
        if (is_array($whereValues))
        {
            //$whereField is an array of key-value pairs
            foreach ($whereValues as $key => $value)
            {
                $m = $m->where($key, $value);
            }
        }
        else
        {
            $m = $m->where('id', $whereValues);
        }
        $recs = $m->distinct()->get();

//$queries = DB::getQueryLog();
//$last_query = end($queries);
//echo var_dump($last_query);
//echo 'last query';

        $ids = array();

        if (is_object($recs) && count($recs->modelKeys()) > 0)
        {
            //records exist so update
            foreach ($recs as $rec)
            {
//                $this->info('updating ' . $updateTable . ' ' . $rec->id . ' ' . PHP_EOL);
                $updateM = DB::table($updateTable)->where('id', $rec->id)->update($insertValues);
                $ids = $rec->id;
            }
        }
        else
        {
//            $this->info('inserting ' . $updateTable . ' ' . PHP_EOL);
            $id = DB::table($updateTable)->insertGetId($insertValues);
            $ids = $id;
//            $this->info($updateTable . ' inserted with id ' . $id . "\n");
        }

        return $ids;
    }

    public function addDivider($parentId)
    {
        $rec = array('label' => 'divider', 'href' => '', 'parent_id' => $parentId, 'icon_class' => '');
        $menuId = DB::table('_db_menus')->insertGetId($rec);
    }

    /**
     * Add a menu item
     * 
     * @param type $label
     * @param type $href
     * @param type $iconClass
     * @param type $parentId Can be the id or the label of the parent menu item
     * @return type
     */
    public function addMenu($label, $href, $iconClass = 'icon-file', $parentId = null)
    {
        if (is_string($parentId))
        {
            $parentId = DB::table('_db_menus')->where('label', $parentId)->first()->id;
        }

        $rec = array('label' => $label, 'href' => $href, 'parent_id' => $parentId, 'icon_class' => $iconClass);
        $menuId = $this->updateOrInsert('_db_menus', $rec, $rec);
        if (is_array($menuId))
        {
            $menuId = $menuId[0];
        }
//        $menuId = DB::table('_db_menus')->insertGetId($rec);
        Log::write('info', $label . ' menu created');
        return $menuId;
    }

    /**
     * Add menu permissions
     * 
     * @param type $menuId
     * @param type $groupName
     */
    public function addMenuPermissions($menuId = null, $groupName = '')
    {
        if (!is_null($menuId))
        {
            $usergroup = DB::table('usergroups')->where('group', $groupName)->first();
            if (is_object($usergroup))
            {
                $usergroupId = $usergroup->id;

                $this->updateOrInsert('_db_menu_permissions', array('menu_id' => $menuId, 'usergroup_id' => $usergroupId));

//                DB::table('_db_menu_permissions')->insertGetId(array('menu_id' => $menuId, 'usergroup_id' => $usergroupId));
            }
        }
    }

    /**
     * Add widget type
     * 
     * @param type $name
     * @return type
     */
    public function addWidgetType($name)
    {
        $widgetTypeId = DB::table('_db_widget_types')->insertGetId(array('name' => $name));
        Log::write(Log::INFO, $name . ' widget type created');
        return $widgetTypeId;
    }

    /**
     * Add display_type
     * 
     * @param type $id
     * @param type $name
     */
    public function addDisplayType($name, $id = null)
    {
        $displayTypes = array('name' => $name);
        if (!is_null($id))
        {
            $displayTypes['id'] = $id;
        }
        $id = DB::table('_db_display_types')->insertGetId($displayTypes);
        Log::write(Log::INFO, $name . ' display types created');
        return $id;
    }

    /**
     * Add display_type
     * 
     * @param type $id
     * @param type $name
     */
    public function addKeyType($name, $id = null)
    {
        $keyTypes = array('name' => $name);
        if (!is_null($id))
        {
            $keyTypes['id'] = $id;
        }
        $id = DB::table('_db_key_types')->insertGetId($keyTypes);
        Log::write(Log::INFO, $name . ' key type created');
        return $id;
    }

    /**
     * Add action to _db_actions
     * 
     * @param type $actionName
     * @return type
     */
    public function addAction($actionName)
    {
        $arr = array("name" => $actionName);
        $id = DB::table('_db_actions')->insertGetId($arr);
        Log::write("success", " - $actionName action created");
        return $id;
    }

    /**
     * Add objects to _db_actions
     * 
     * @param type $actionName
     * @return type
     */
    public function addObject($objectName)
    {
        $arr = array("name" => $objectName);
        $id = DB::table('_db_objects')->insertGetId($arr);
        Log::write("success", " - $objectName object created");
        return $id;
    }

    /**
     * populate _db_views
     * 
     * @param type $viewName
     */
    public function addView($viewName)
    {
        $arr = array("name" => $viewName);
        $viewId = DB::table('_db_views')->insertGetId($arr);
        Log::write("success", " - $viewName view inserted");
    }

    /**
     * Populate table _db_pages
     * 
     * @param type $viewId
     * @param type $doPermissions Will also populate permissions tables if true
     * 
     */
    public function populateTableActions($doPermissions = false)
    {
        try
        {
            DB::table('_db_pages')->delete();

            $tables = DB::table('_db_tables')->get();
            $actions = DB::table('_db_actions')->get();
            $views = DB::table('_db_views')->get();

            if ($doPermissions)
            {
                $users = DB::table('users')->get();
                $usergroups = DB::table('usergroups')->get();
            }
            
            $pageTypes = $this->getOptionType('adminpages'); //frontendpages
            $pageTypeId = $pageTypes[0]['id'];
            
            foreach ($views as $view)
            {
                foreach ($tables as $table)
                {
                    foreach ($actions as $action)
                    {
                        $slug = strtolower($table->name . '_' . $action->name);
                        Log::write("info", "Linking table " . $table->name . ", \t view " . $view->name . ", \t action " . $action->name);
                        $arrTav = array('table_id' => $table->id, 'action_id' => $action->id,
                            'view_id' => $view->id, 'page_size' => 10, 'title' => $table->name, 'slug' => $slug,
                            'page_type_id'=>$pageTypeId);

                        DB::table('_db_pages')->insert($arrTav);
                        /*
                          if ($doPermissions)
                          {
                          foreach ($users as $user)
                          {
                          $arr = array('table_id' => $table->id, 'action_id' => $action->id, 'user_id' => $user->id);
                          DB::table('_db_user_permissions')->insert($arr);
                          Log::write("success", "Granted user " . $user->username . " \t action " . $action->name . " on \t table " . $table->name);
                          }
                          foreach ($usergroups as $usergroup)
                          {
                          $arr = array('table_id' => $table->id, 'action_id' => $action->id, 'usergroup_id' => $usergroup->id);
                          DB::table('_db_usergroup_permissions')->insert($arr);
                          Log::write("success", "Granted usergroup " . $usergroup->group . " \t action " . $action->name . " on table \t" . $table->name);
                          }
                          }
                         * 
                         */
                    }
                }
            }
        }
        catch (Exception $e)
        {
            Log::write("success", $e->getMessage());
            $message = "Error inserting record into table.";
            Log::write("success", $message);
            throw new Exception($message, 1, $e);
        }
    }

    /**
     * Replace _ with spaces and make first character of each word uppercase
     * 
     * @param type $name
     */
    public function makeLabel($name)
    {
        return ucwords(str_replace('_', ' ', $name));
    }

    /**
     * Returns varchar if fieldType = varchar(100) etc.
     */
    public function getFieldType($fieldType)
    {
        $start = strpos($fieldType, '(');
        if ($start > 0)
        {
            $fieldType = substr($fieldType, 0, $start);
            Log::write("success", "fieldtype : $fieldType");
        }
        return $fieldType;
    }

    /**
     * Returns 100 if fieldType = varchar(100) etc.
     */
    public function getFieldLength($fieldType)
    {
        $start = strpos($fieldType, '(') + 1;
        $len = null;
        if ($start > 0)
        {
            $count = strpos($fieldType, ')') - $start;
            $len = substr($fieldType, $start, $count);
            //$this->__log("success", "fieldtype : $fieldType, start : $start, count : $count, len : $len");
        }

        return $len;
    }

    /**
     * Try and calculate the width of the widget to display the field in 
     */
    public function getFieldWidth($fieldType, $fieldLength)
    {
        return 220;
    }

    /**
     * Try and calculate the best widget to display the field in. Define the widget in json
     */
    public function getFieldWidget($fieldType, $fieldLength)
    {
        return ""; //'{widget" : "input", "attributes" : {"type" : "text"}}';
    }

    public function getDisplayType($colRec, $types)
    {

        $displayTypeId = $types['edit'];

        if ($colRec['name'] == "created_at" || $colRec['name'] == "updated_at")
        {
            $displayTypeId = $types['nodisplay'];
        }
        return $displayTypeId;
    }

}

?>
