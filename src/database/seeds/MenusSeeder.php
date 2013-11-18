<?php namespace Laravella\Crud;

use Laravella\Crud\Log;
use \Seeder;
use \DB;

class SeedMenus extends CrudSeeder 
{

	public function run()
	{

		DB::table('_db_menus')->delete();
		DB::table('_db_menu_permissions')->delete();
                
                //setting the very top level of a new menu as a parent of itself, so that it's not orphaned
                $topMenuId = $this->addMenu('TopMenu', '', 'icon-file', null);
                DB::table('_db_menus')->where("id", $topMenuId)->update(array("parent_id"=>$topMenuId));

                $contentId = $this->addMenu('Contents', '', 'icon-file', $topMenuId);
                $this->addMenu('Contents', '/db/select/contents', 'icon-file', $contentId);
                $this->addMenu('Post Categories', '/db/select/categories', 'icon-file', $contentId);
                
                $this->addDivider($contentId);
                $this->addMenu('Media', '/db/select/medias', 'icon-file', $contentId);
                $this->addMenu('Collections', '/db/select/mcollections', 'icon-file', $contentId);
                $this->addMenu('Galleries', '/db/select/galleries', 'icon-file', $contentId);
                
//                $contentId = $this->__addMenu('Contents', '', 'icon-file', $topMenuId);
//                $this->__addMenu('Pages', '/admin/pages/index', 'icon-file', $contentId);
//                $this->__addMenu('Posts', '/admin/posts/index', 'icon-file', $contentId);
//                $this->__addMenu('Post Categories', '/admin/categories/index', 'icon-file', $contentId);
//                $this->__addMenu('divider', '/db/select/users', 'icon-file', $contentId);
//                $this->__addMenu('Media Upload', '/admin/medias/index', 'icon-file', $contentId);
//                $this->__addMenu('Media', '/db/select/medias', 'icon-file', $contentId);
//                $this->__addMenu('Collections', '/db/select/mcollections', 'icon-file', $contentId);
//                $this->__addMenu('Galleries', '/db/select/galleries', 'icon-file', $contentId);
                
                $adminId = $this->addMenu('Admin', '/db', 'icon-file', $topMenuId);
                $this->addMenu('Users', '/db/select/users', 'icon-file', $adminId);
                $this->addMenu('Groups', '/db/select/usergroups', 'icon-file', $adminId);
//                $this->__addMenu('Users Groups', '/db/select/users_groups', 'icon-file', $adminId);
//                $this->__addMenu('User Permissions', '/', 'icon-file', $adminId);
//                $this->__addMenu('Group Permissions', '/', 'icon-file', $adminId);
                $this->addDivider($adminId);
                $this->addMenu('Menus', '/db/select/_db_menus', 'icon-file', $adminId);
                $this->addMenu('Menu Permissions', '/db/select/_db_menu_permissions', 'icon-file', $adminId);
                $this->addMenu('divider', null, '', $adminId);
                $this->addMenu('Options', '/db/select/_db_options', 'icon-file', $adminId);
                
                $metaDataId = $this->addMenu('Meta Data', '/db', 'icon-file', $topMenuId);
                $this->addMenu('Home', '/', 'icon-file', $metaDataId);
                
                $this->addDivider($metaDataId);
                $this->addMenu('Pages', '/db/select/_db_pages', 'icon-file', $metaDataId);
                $this->addMenu('Tables', '/db/select/_db_tables', 'icon-file', $metaDataId);
                $this->addMenu('Fields', '/db/select/_db_fields', 'icon-file', $metaDataId);
                $this->addMenu('Keys', '/db/select/_db_key_fields', 'icon-file', $metaDataId);
                $this->addMenu('Actions', '/db/select/_db_actions', 'icon-file', $metaDataId);
                $this->addMenu('Views', '/db/select/_db_views', 'icon-file', $metaDataId);
                $this->addDivider($metaDataId);
                $this->addMenu('Key Types', '/db/select/_db_key_types', 'icon-file', $metaDataId);
                $this->addMenu('Option Types', '/db/select/_db_option_types', 'icon-file', $metaDataId);
                $this->addMenu('Display Types', '/db/select/_db_display_types', 'icon-file', $metaDataId);
                $this->addMenu('Widget Types', '/db/select/_db_widget_types', 'icon-file', $metaDataId);
                $this->addDivider($metaDataId);
                $this->addMenu('Objects', '/db/select/_db_objects', 'icon-file', $metaDataId);
                $this->addMenu('Assets', '/db/select/_db_assets', 'icon-file', $metaDataId);
                $this->addMenu('Events', '/db/select/_db_events', 'icon-file', $metaDataId);
                $this->addDivider($metaDataId);
                $this->addMenu('Log', '/db/select/_db_logs', 'icon-file', $metaDataId);
                $this->addMenu('Audit', '/db/select/_db_audit', 'icon-file', $metaDataId);
                
                $this->addMenuPermissions($contentId, 'superadmin');
                $this->addMenuPermissions($contentId, 'admin');
                
                $this->addMenuPermissions($metaDataId, 'superadmin');
                $this->addMenuPermissions($metaDataId, 'admin');
                
                $this->addMenuPermissions($adminId, 'superadmin');
                $this->addMenuPermissions($adminId, 'admin');
                
		
	}
}
?>