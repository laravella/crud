<?php

use Laravella\Crud\Log;

class SeedUsers extends Seeder {

    public function run()
    {
        $password = rand(23450987, 234509870);

        $password = md5($password);
        
        $shortPassword = substr($password,0,8);
        
        echo "-- password : $shortPassword --";
        
        $hashPass = Hash::make($shortPassword);

        $adminUser = array('username' => 'admin', 'password' => $hashPass, 'email' => 'admin@yourwebsite.com'); //Config::get('crud::app.setup_user');

        $group = DB::table('groups')->where('name', 'Admins')->first();
        
        $userGroup = DB::table('usergroups')->where('group', 'Admins')->first();

        $adminUser['activated'] = true;
        $adminUser['api_token'] = makeApiKey();

        DB::table('users')->delete();

        $userId = DB::table('users')->insertGetId($adminUser);

        DB::table('users')->update(array('id' => $userId, 'usergroup_id' => $userGroup->id));
        
        DB::table('users_groups')->insert(array('user_id' => $userId, 'group_id' => $group->id));
    }

}

?>