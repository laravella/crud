<?php

namespace Laravella\Crud;

use Laravella\Crud\Log;
use \Seeder;
use \DB;
use \Config;
use \App;

class JsonSeeder extends CrudSeeder {

    /**
     * 
      //key-values pairs
      _db_options;

      //types
      _db_option_types;
      _db_display_types;
      _db_widget_types;
      _db_key_types;
      _db_actions;
      _db_severities;
      _db_views;

      //complex tables
      _db_assets;
      _db_menus;
      _db_page_members
      _db_key_fields;
      _db_keys;
      _db_tables;

      //n-n
      _db_page_assets
      _db_user_permissions #user-pages
      _db_usergroup_permissions #usergroup-pages
     * 
     */
    public function run()
    {
        $objectsPath = Config::get('app.objects');
        $json = json_decode(file_get_contents($objectsPath), true);
        $this->addOptionTypes($json['types']);
        $this->addOptions($json['options']);
        $this->addKeys($json['keys']);
        $this->updateFields($json['fields']);
        $this->linkPageToTables($json['page_tables']);
        $this->addData($json['data']);
        $this->addMenusJson($json['menus']);

//        App::instance('meta', $json);
    }

    /**
     * 
     * 
     * @param type $menus
     */
    public function addMenusJson($menus, $parentId = null)
    {
        foreach($menus as $menu) {
            try {
                $slug = self::coa($menu, 'slug');
                $pageId = $this->getId('_db_pages', 'slug', $slug);
                $subMenus = self::coa($menu, 'sub_menus');
                $usergroups = self::coa($menu, 'usergroups');
                $menuA = $this->buildArray($menu, array("icon_class", "label", "href", "weight")); //array('page_id'=>$pageId);
                $menuA['page_id'] = $pageId;
                $menuA['parent_id'] = $parentId;
                $mId = DB::table('_db_menus')->insertGetId($menuA);
                if (!empty($usergroups)) 
                {
                    echo "\n";
                    foreach($usergroups as $usergroup) {
                        echo "linking menu ".$mId." to ".$usergroup."\n";
                        $this->addMenuPermissions($mId, $usergroup);
                    }
                    die;
                }
                if (!empty($subMenus)) 
                {
                    $this->addMenusJson($subMenus, $mId);
                }
            } catch (Exception $e) {
                //echo $e->getMessage();
            }
        }
    }

    /**
     * Add any type of data, particularly for tables that don't contain foreign keys
     * 
     * @param type $tables
     */
    public function addData($tables)
    {
        foreach ($tables as $table => $data)
        {
            DB::table($table)->insert($data);
        }
    }

}

?>