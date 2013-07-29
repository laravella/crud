<?php

class SeedUsers extends Seeder
{


	public function run()
	{

		$adminUser = Config::get('crud::app.setup_user');

		$adminGroup = DB::table('_db_usergroups')->where('group','admin')->first();

		$adminUser['usergroup_id'] = (int) $adminGroup->id;
		$adminUser->activated = true;
		$adminUser->api_token = makeApiKey();

		DB::table('users')->delete();
		
		DB::table('users')->insert($adminUser);
		
	}
}