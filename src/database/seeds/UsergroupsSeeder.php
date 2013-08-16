<?php namespace Laravella\Crud;

use Laravella\Crud\Log;
use \Seeder;
use \DB;

class SeedUsergroups extends Seeder
{


	public function run()
	{

		DB::table('usergroups')->delete();
                
                $group = array('group'=>'superadmin');     //can change permissions
		DB::table('usergroups')->insert($group);
                Log::write('info', 'superadmin usergroup created');
                
                $group = array('group'=>'admin');          //can edit admin tables except permissions
		DB::table('usergroups')->insert($group);
                Log::write('info', 'admin usergroup created');
                
                $group = array('group'=>'manager');      //can moderate
		DB::table('usergroups')->insert($group);
                Log::write('info', 'manager usergroup created');
                
                $group = array('group'=>'user');           //can post articles
		DB::table('usergroups')->insert($group);
                Log::write('info', 'user usergroup created');
                
                $group = array('group'=>'guest');          //can make comments
		DB::table('usergroups')->insert($group);
                Log::write('info', 'guest usergroup created');
		
	}
}
?>