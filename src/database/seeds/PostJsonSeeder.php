<?php

namespace Laravella\Crud;

use Laravella\Crud\Log;
use \Seeder;
use \DB;
use \Config;
use \App;

class PostJsonSeeder extends CrudSeeder {

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
        $objectsPaths = Config::get('app.postseed');
        foreach($objectsPaths as $objectsPath) {
            $json = json_decode(file_get_contents($objectsPath), true);
            $this->addKeys($json['keys']);
//            $this->updateFields($json['fields']);
            $this->linkPageToTables($json['page_tables']);
            $this->addMenusJson($json['menus']);
//            $this->addTableWidgets($json['table_widgets']);
        }
//        App::instance('meta', $json);
    }

}

?>