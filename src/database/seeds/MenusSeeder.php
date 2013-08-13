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
        
        private function __addMenuPermissions($menuId, $groupName) {
            $usergroup = DB::table('usergroups')->where('group', $groupName)->first();
            if (is_object($usergroup)) {
                $usergroupId = $usergroup->id;
                DB::table('_db_menu_permissions')->insertGetId(array('menu_id'=>$menuId, 'usergroup_id'=>$usergroupId));
            }
        }
        
	public function run()
	{

		DB::table('_db_menus')->delete();
		DB::table('_db_menu_permissions')->delete();
                
                $topMenuId = $this->__addMenu('TopMenu', '', 'icon-file', null);
                DB::table('_db_menus')->where("id", $topMenuId)->update(array("parent_id"=>$topMenuId));
                
                $adminId = $this->__addMenu('Admin', '/db', 'icon-file', $topMenuId);
                $this->__addMenu('Users', '/db/select/users', 'icon-file', $adminId);
                $this->__addMenu('Groups', '/db/select/groups', 'icon-file', $adminId);
                $this->__addMenu('Users Groups', '/db/select/users_groups', 'icon-file', $adminId);
                $this->__addMenu('User Permissions', '/', 'icon-file', $adminId);
                $this->__addMenu('Group Permissions', '/', 'icon-file', $adminId);
                $this->__addMenu('divider', null, '', $metaDataId);
                $this->__addMenu('Menus', '/db/select/_db_menus', 'icon-file', $adminId);
                $this->__addMenu('Menu Permissions', '/db/select/_db_menu_permissions', 'icon-file', $adminId);
                //users
                //group permissions
                //user permissions
                
                $metaDataId = $this->__addMenu('Meta Data', '/db', 'icon-file', $topMenuId);
                $this->__addMenu('Home', '/', 'icon-file', $metaDataId);
                $this->__addMenu('divider', null, '', $metaDataId);
                $this->__addMenu('Tables', '/db/select/_db_tables', 'icon-file', $metaDataId);
                $this->__addMenu('Fields', '/db/select/_db_fields', 'icon-file', $metaDataId);
                $this->__addMenu('Actions', '/db/select/_db_actions', 'icon-file', $metaDataId);
                $this->__addMenu('Views', '/db/select/_db_views', 'icon-file', $metaDataId);
                $this->__addMenu('Action Views', '/db/select/_db_table_action_views', 'icon-file', $metaDataId);
                $this->__addMenu('divider', null, '', $metaDataId);
                $this->__addMenu('Log', '/db/select/_db_log', 'icon-file', $metaDataId);
                $this->__addMenu('Audit', '/db/select/_db_audit', 'icon-file', $metaDataId);
//                $this->__addMenu('divider', null, '', $metaDataId);
//                $this->__addMenu('Install', '/dbinstall/install', 'icon-file', $metaDataId);
//                $this->__addMenu('Reinstall', '/dbinstall/reinstall', 'icon-file', $metaDataId);
                
                $this->__addMenuPermissions($metaDataId, 'superadmin');
                $this->__addMenuPermissions($metaDataId, 'admin');
                
                $this->__addMenuPermissions($adminId, 'superadmin');
                $this->__addMenuPermissions($adminId, 'admin');
                
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