<?php

namespace Laravella\Crud;

use Laravella\Crud\Log;
use \Seeder;
use \DB;
use \Config;
use \App;

class PreJsonSeeder extends CrudSeeder {

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
        
        DB::table('_db_actions')->delete();
        DB::table('_db_display_types')->delete();
        DB::table('_db_key_types')->delete();
//        DB::table('_db_log')->delete();
        DB::table("_db_menus")->delete();
        DB::table("_db_menu_permissions")->delete();
        DB::table('_db_objects')->delete();
        DB::table('_db_page_tables')->delete();
        DB::table('_db_severities')->delete();
        DB::table('_db_widget_types')->delete();
        
        $objectsPaths = Config::get('app.preseed');
        foreach($objectsPaths as $objectsPath) {
            $json = json_decode(file_get_contents($objectsPath), true);
            $this->addData($json['data']);
            $this->addOptionTypes($json['types']);
            $this->addOptions($json['options']);
        }
        
//        App::instance('meta', $json);
    }

}

?>