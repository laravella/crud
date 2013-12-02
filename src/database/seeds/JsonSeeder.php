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
        $this->updateFields($json['fields']);
//        App::instance('meta', $json);
    }
    
}
?>