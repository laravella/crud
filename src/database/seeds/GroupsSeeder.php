<?php

use Laravella\Crud\Log;

class SeedGroups extends Seeder
{


	public function run()
	{

		DB::table('groups')->delete();
                
                $group = array('name'=>'SuperAdmins');     //can change permissions
		DB::table('groups')->insert($group);
                Log::write('info', 'SuperAdmins usergroup created');
                
                $group = array('name'=>'Admins');          //can edit admin tables except permissions
		DB::table('groups')->insert($group);
                Log::write('info', 'Admins usergroup created');
                
                $group = array('name'=>'SuperUsers');      //can moderate
		DB::table('groups')->insert($group);
                Log::write('info', 'SuperUsers usergroup created');
                
                $group = array('name'=>'Users');           //can post articles
		DB::table('groups')->insert($group);
                Log::write('info', 'Users usergroup created');
                
                $group = array('name'=>'Guests');          //can make comments
		DB::table('groups')->insert($group);
                Log::write('info', 'Guests usergroup created');
		
	}
}
?>