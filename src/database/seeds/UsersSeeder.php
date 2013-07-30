<?php

use Laravella\Crud\Log;

class SeedUsers extends Seeder {

    public function run()
    {
        $password = rand(23450987, 234509870);

        $password = md5($password);

        $adminUser = array('username' => 'admin', 'password' => substr($password,0,8), 'email' => 'admin@yourwebsite.com'); //Config::get('crud::app.setup_user');

        $adminGroup = DB::table('usergroups')->where('group', 'Admins')->first();

        $adminUser['activated'] = true;
        $adminUser['api_token'] = $password;

        DB::table('users')->delete();

        $userId = DB::table('users')->insertGetId($adminUser);

        DB::table('users_groups')->insert(array('user_id' => $userId, 'group_id' => $adminGroup->id));
    }

}

?>