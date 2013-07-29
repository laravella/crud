<?php

use Laravella\Crud\Log;

class SeedActions extends Seeder {

    public function run()
    {
        $arr = array("name" => "getSelect");
        DB::table('_db_actions')->insert($arr);
        Log::write("success", " - getSelect action created");

        $arr = array("name" => "getInsert");
        DB::table('_db_actions')->insert($arr);
        Log::write("success", " - getInsert action created");

        $arr = array("name" => "getEdit");
        DB::table('_db_actions')->insert($arr);
        Log::write("success", " - getEdit action created");

        $arr = array("name" => "postEdit");
        DB::table('_db_actions')->insert($arr);
        Log::write("success", " - postEdit action created");

        $arr = array("name" => "postDelete");
        DB::table('_db_actions')->insert($arr);
        Log::write("success", " - postDelete action created");

        $arr = array("name" => "getSearch");
        DB::table('_db_actions')->insert($arr);
        Log::write("success", " - getSearch action created");
    }

}
?>