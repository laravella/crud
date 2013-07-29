<?php

use Laravella\Crud\Log;

class SeedSeverities extends Seeder {

    public function run()
    {

        DB::table('_db_severities')->delete();
        
        $arr = array("name" => "success");
        $viewId = DB::table('_db_severities')->insertGetId($arr);
        Log::write("success", " - 'success' severity inserted");

        $arr = array("name" => "info");
        $viewId = DB::table('_db_severities')->insertGetId($arr);
        Log::write("success", " - 'info' severity inserted");

        $arr = array("name" => "warning");
        $viewId = DB::table('_db_severities')->insertGetId($arr);
        Log::write("success", " - 'warning' severity inserted");

        $arr = array("name" => "important");
        $viewId = DB::table('_db_severities')->insertGetId($arr);
        Log::write("success", " - 'error' severity inserted");
    }

}
?>