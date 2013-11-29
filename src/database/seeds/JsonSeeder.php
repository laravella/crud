<?php namespace Laravella\Crud;

use Laravella\Crud\Log;
use \Seeder;
use \DB;
use \Config;
use \App;


class JsonSeeder extends CrudSeeder {
    
    public function run()
    {
        $objectsPath = Config::get('app.objects');
        $json = json_decode(file_get_contents($objectsPath), true);
        $this->addKeys($json['keys']);
//        App::instance('meta', $json);
    }

    public function addKeys($keys) 
    {
        foreach ($keys as $key) {
            $this->addKey($key['pk_field'], $key['pk_display_field'], $key['fk_field'], $key['fk_display_field'], $key['key_type'], $key['order']);
        }
    }
    
    public function addKey($pk_field, $pk_display_field, $fk_field, $fk_display_field, $key_type, $order) 
    {
            $pk_fullname = explode('.', $pk_field);
            $fk_fullname = explode('.', $fk_field);
            
            $pk_table = $pk_fullname[0];
            $pk_field = $pk_fullname[1];
            
            $fk_table = $fk_fullname[0];
            $fk_field = $fk_fullname[1];
            
            $this->updateReference($fk_table, $fk_field, $pk_table, $pk_field, $pk_display_field);
    }
    
}
?>