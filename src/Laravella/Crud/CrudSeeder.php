<?php namespace Laravella\Crud;

use Laravella\Crud\Log;
use \Seeder;
use \DB;


class CrudSeeder extends Seeder {

    public function addAction($actionName) {
        $arr = array("name" => $actionName);
        $id = DB::table('_db_actions')->insertGetId($arr);
        Log::write("success", " - $actionName action created");
        return $id;
    }

}

?>
