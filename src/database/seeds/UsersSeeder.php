<?php namespace Laravella\Crud;

use Laravella\Crud\Log;
use \Seeder;
use \DB;
use \Hash;

class SeedUsers extends CrudSeeder {

    private function createUser($groupName, $name, $password, $email, $firstName, $lastName) {
        
        $this->command->info($groupName.' '.$name);
        
        Log::write(Log::INFO, '['.$groupName.'] '.$name);

        $hashPass = Hash::make($password);
        
        //$group = DB::table('groups')->where('name', $groupName)->first();
        $userGroup = DB::table('usergroups')->where('group', $groupName)->first();
    
        $adminUser = array('username' => $name, 'password' => $hashPass, 'email' => $email, 'first_name'=> $firstName, 'last_name'=>$lastName); //Config::get('crud::app.setup_user');
        $adminUser['activated'] = true;
        $adminUser['api_token'] = $this->makeApiKey();
        if (is_object($userGroup)) {
            $adminUser['usergroup_id'] = $userGroup->id;
            $this->command->info('usergroup id is : '.$userGroup->id);
        }
                
        $userId = DB::table('users')->insertGetId($adminUser);

        if (is_object($userGroup)) {
//            DB::table('users_groups')->insert(array('user_id' => $userId, 'usergroup_id' => $userGroup->id));
        }   
        
        return $userId;
        
    }
    
    private function makeApiKey() {
        $password = rand(23450987, 234509870);
        $password = md5($password);
        return $password;
        
    }
    
    public function run()
    {
        DB::table('users')->delete();
        
        $password = $this->makeApiKey();
        
        $shortPassword = substr($password,0,8);
        
        $this->createUser('superadmin', 'superadmin', 'ravel', 'superadmin@yourwebsite.com', 'super', 'admin');
        
        $this->createUser('admin', 'admin', 'ravel', 'admin@yourwebsite.com', 'admin', 'admin');
        
        //echo ;
        Log::write(Log::INFO, "-- password : $shortPassword --");
        
        //admin ravel 'admin@yourwebsite.com'
        
    }

}

?>