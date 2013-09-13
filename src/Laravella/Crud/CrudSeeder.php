<?php namespace Laravella\Crud;

use Laravella\Crud\Log;
use \Seeder;
use \DB;

class CrudSeeder extends Seeder {

    /**
     * Add a severity
     * 
     * @param type $severity
     */
    public function addSeverity($severity) {
        $arr = array("name" => $severity);
        $viewId = DB::table('_db_severities')->insertGetId($arr);
        Log::write("success", " - $severity severity inserted");
    }
    
    /**
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
    public function addOptionType($name, $parentId=null)
    {
        $optionType = array('name' => $name, 'parent_id' => $parentId);
        $optionTypeId = DB::table('_db_option_types')->insertGetId($optionType);
        Log::write(Log::INFO, $name . ' option type created');
        return $optionTypeId;
    }

    /**
     * Add a new usergroup
     * 
     * @param type $groupName
     */
    public function addGroup($groupName) {
        $group = array('name' => $groupName);     //can change permissions
        DB::table('groups')->insert($group);
        Log::write('info', $groupName.' usergroup created');
    }
    
    /**
     * Updates a field or inserts a record if key does not exist
     * 
     * @param type $updateTable
     * @param type $setField
     * @param type $setValue
     * @param type $whereValues an array of key value pairs , or an id
     * @param type $insertValues Used as a single value if whereField is a string, else excluded
     */
    public function updateOrInsert($updateTable, $whereValues, array $insertValues)
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
        $recs = $m->distinct()->get();

//$queries = DB::getQueryLog();
//$last_query = end($queries);
//echo var_dump($last_query);
//echo 'last query';

        if (is_object($recs) && count($recs->modelKeys()) > 0)
        {
            //records exist so update
            foreach ($recs as $rec)
            {
                echo 'updating ' . $updateTable . ' ' . $rec->id . ' ' . PHP_EOL;
                $updateM = DB::table($updateTable)->where('id', $rec->id)->update($insertValues);
            }
        }
        else
        {
            echo 'inserting ' . $updateTable . ' ' . PHP_EOL;
            $id = DB::table($updateTable)->insertGetId($insertValues);
            echo $updateTable . ' inserted with id ' . $id . "\n";
        }
    }

    /**
     * Add a menu item
     * 
     * @param type $label
     * @param type $href
     * @param type $iconClass
     * @param type $parentId
     * @return type
     */
    public function addMenu($label, $href, $iconClass = 'icon-file', $parentId = null)
    {
        $group = array('label' => $label, 'href' => $href, 'parent_id' => $parentId, 'icon_class' => $iconClass);
        $menuId = DB::table('_db_menus')->insertGetId($group);
        Log::write('info', $label . ' menu created');
        return $menuId;
    }

    /**
     * Add menu permissions
     * 
     * @param type $menuId
     * @param type $groupName
     */
    public function addMenuPermissions($menuId, $groupName)
    {
        $usergroup = DB::table('usergroups')->where('group', $groupName)->first();
        if (is_object($usergroup))
        {
            $usergroupId = $usergroup->id;
            DB::table('_db_menu_permissions')->insertGetId(array('menu_id' => $menuId, 'usergroup_id' => $usergroupId));
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
        if (!is_null($id)) {
            $displayTypes['id']  = $id; 
        }
        DB::table('_db_display_types')->insert($displayTypes);
        Log::write(Log::INFO, $name . ' display types created');
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
     * populate _db_views
     * 
     * @param type $viewName
     */
    public function addView($viewName)
    {
        $arr = array("name" => $viewName);
        $viewId = DB::table('_db_views')->insertGetId($arr);
        Log::write("success", " - $viewName view inserted");
        $this->__populateTableActions($viewId, true);
    }

    /**
     * Populate table _db_table_action_views
     * 
     * @param type $viewId
     * @param type $doPermissions Will also populate permissions tables if true
     * 
     */
    private function __populateTableActions($viewId, $doPermissions = false)
    {
        try
        {
            $tables = DB::table('_db_tables')->get();
            $actions = DB::table('_db_actions')->get();

            if ($doPermissions)
            {
                $users = DB::table('users')->get();
                $usergroups = DB::table('groups')->get();
            }
            foreach ($tables as $table)
            {
                foreach ($actions as $action)
                {
                    $arr = array('table_id' => $table->id, 'action_id' => $action->id, 'view_id' => $viewId, 'page_size' => 10, 'title' => $table->name);

                    DB::table('_db_table_action_views')->insert($arr);
                    if ($doPermissions)
                    {
                        foreach ($users as $user)
                        {
                            $arr = array('table_id' => $table->id, 'action_id' => $action->id, 'user_id' => $user->id);
                            DB::table('_db_user_permissions')->insert($arr);
                        }
                        foreach ($usergroups as $usergroup)
                        {
                            $arr = array('table_id' => $table->id, 'action_id' => $action->id, 'usergroup_id' => $usergroup->id);
                            DB::table('_db_usergroup_permissions')->insert($arr);
                        }
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
    
}

?>
