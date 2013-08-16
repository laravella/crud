<?php namespace Laravella\Crud;

use Laravella\Crud\Log;

class SeedViews extends Seeder {

    public function run()
    {
        DB::table('_db_views')->delete();

        $arr = array("name" => "crud::dbview");
        $viewId = DB::table('_db_views')->insertGetId($arr);
        Log::write("success", " - crud::dbview view inserted");
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