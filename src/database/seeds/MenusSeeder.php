<?php

use Laravella\Crud\Log;

class SeedMenus extends Seeder
{

        private function __addMenu($label, $href, $iconClass = 'icon-file', $parentId = null) {
            $group = array('label'=>$label, 'href'=>$href, 'parent_id'=>$parentId, 'icon_class'=>$iconClass);     
            $menuId = DB::table('_db_menus')->insertGetId($group);
            Log::write('info', $label.' menu created');
            return $menuId;
        }
        
	public function run()
	{

		DB::table('_db_menus')->delete();
                
                $topMenuId = $this->__addMenu('TopMenu', '', null);
                
                $adminId = $this->__addMenu('Admin', '/db', $topMenuId);
                $this->__addMenu('Users', '/db/select/users', $adminId);
                $this->__addMenu('Groups', '/db/select/groups', $adminId);
                $this->__addMenu('Users Groups', '/db/select/users_groups', $adminId);
                $this->__addMenu('User Permissions', '/', $adminId);
                $this->__addMenu('Group Permissions', '/', $adminId);
                //users
                //group permissions
                //user permissions
                
                $metaDataId = $this->__addMenu('Meta Data', '/db', $topMenuId);
                $this->__addMenu('Home', '/', $metaDataId);
                $this->__addMenu('divider', null, $metaDataId);
                $this->__addMenu('Tables', '/db/select/_db_tables', $metaDataId);
                $this->__addMenu('Fields', '/db/select/_db_tables', $metaDataId);
                $this->__addMenu('Actions', '/db/select/_db_tables', $metaDataId);
                $this->__addMenu('Views', '/db/select/_db_tables', $metaDataId);
                $this->__addMenu('Action Views', '/db/select/_db_tables', $metaDataId);
                $this->__addMenu('divider', null, $metaDataId);
                $this->__addMenu('Log', '/db/select/_db_log', $metaDataId);
                $this->__addMenu('Audit', '/db/select/_db_audit', $metaDataId);
                $this->__addMenu('divider', null, $metaDataId);
                $this->__addMenu('Install', '/dbinstall/install', $metaDataId);
                $this->__addMenu('Reinstall', '/dbinstall/reinstall', $metaDataId);
                
                //tables
                //fields
                //actions
                //views
                //action views
                //log
                //audit
                //install
                //reinstall
		
	}
}
?>