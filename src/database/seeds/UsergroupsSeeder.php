<?php

use Laravella\Crud\Log;

class SeedUsergroups extends Seeder
{


	public function run()
	{

		DB::table('usersgroups')->delete();
                
                $group = array('group'=>'SuperAdmins');     //can change permissions
		DB::table('usergroups')->insert($group);
                Log::write('info', 'SuperAdmins usergroup created');
                
                $group = array('group'=>'Admins');          //can edit admin tables except permissions
		DB::table('usergroups')->insert($group);
                Log::write('info', 'Admins usergroup created');
                
                $group = array('group'=>'SuperUsers');      //can moderate
		DB::table('usergroups')->insert($group);
                Log::write('info', 'SuperUsers usergroup created');
                
                $group = array('group'=>'Users');           //can post articles
		DB::table('usergroups')->insert($group);
                Log::write('info', 'Users usergroup created');
                
                $group = array('group'=>'Guests');          //can make comments
		DB::table('usergroups')->insert($group);
                Log::write('info', 'Guests usergroup created');
		
	}
}
?>