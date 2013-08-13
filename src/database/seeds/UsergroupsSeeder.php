<?php

use Laravella\Crud\Log;

class SeedUsergroups extends Seeder
{


	public function run()
	{

		DB::table('usergroups')->delete();
                
                $group = array('group'=>'superadmin');     //can change permissions
		DB::table('usergroups')->insert($group);
                Log::write('info', 'SuperAdmins usergroup created');
                
                $group = array('group'=>'admin');          //can edit admin tables except permissions
		DB::table('usergroups')->insert($group);
                Log::write('info', 'Admins usergroup created');
                
                $group = array('group'=>'manager');      //can moderate
		DB::table('usergroups')->insert($group);
                Log::write('info', 'SuperUsers usergroup created');
                
                $group = array('group'=>'user');           //can post articles
		DB::table('usergroups')->insert($group);
                Log::write('info', 'Users usergroup created');
                
                $group = array('group'=>'guest');          //can make comments
		DB::table('usergroups')->insert($group);
                Log::write('info', 'Guests usergroup created');
		
	}
}
?>