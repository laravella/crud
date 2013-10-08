<?php namespace Laravella\Crud;

use Laravella\Crud\Log;
use \Seeder;
use \DB;
use \Hash;

class SeedUsers extends CrudSeeder {

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