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
        
//        App::instance('meta', $json);
    }
    
    public function addData($tables) 
    {
        foreach ($tables as $table=>$data) {
            DB::table($table)->insert($data);
        }
    }

}

?>