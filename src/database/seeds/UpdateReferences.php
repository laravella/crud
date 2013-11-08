<?php namespace Laravella\Crud;

use Laravella\Crud\Log;
use \Seeder;
use \DB;

class UpdateReferences extends CrudSeeder {

    /**
     * 
     * 
     * @param type $log
     * @throws Exception
     */
    public function run()
    {
        try
        {
            // create foreign key references with
            // log, fkTableName, fkFieldName, pkTableName, pkFieldName, pkDisplayFieldName

            DB::table('_db_key_fields')->delete();

            $this->updateReference('_db_fields', 'table_id', '_db_tables', 'id', 'name');
            $this->updateReference('_db_fields', 'display_type_id', '_db_display_types', 'id', 'name');
            $this->updateReference('_db_fields', 'widget_type_id', '_db_widget_types', 'id', 'name');
            echo "_db_fields done. \n";
            
            $this->updateReference('_db_pages', 'view_id', '_db_views', 'id', 'name');
            $this->updateReference('_db_pages', 'table_id', '_db_tables', 'id', 'name');
            $this->updateReference('_db_pages', 'action_id', '_db_actions', 'id', 'name');
            echo "_db_pages done. \n";
            
            $this->updateReference('users', 'usergroup_id', 'usergroups', 'id', 'group');
            echo "users done. \n";
            
            $this->updateReference('usergroups', 'parent_id', 'usergroups', 'id', 'group');
            echo "usergroups done. \n";
            
            $this->updateReference('_db_user_permissions', 'user_id', 'users', 'id', 'username');
            $this->updateReference('_db_user_permissions', 'table_id', '_db_tables', 'id', 'name');
            $this->updateReference('_db_user_permissions', 'action_id', '_db_actions', 'id', 'name');
            echo "_db_user_permissions done. \n";

//            $this->updateReference('_db_usergroup_permissions', 'usergroup_id', 'groups', 'id', 'name');
            $this->updateReference('_db_usergroup_permissions', 'table_id', '_db_tables', 'id', 'name');
            $this->updateReference('_db_usergroup_permissions', 'action_id', '_db_actions', 'id', 'name');
            echo "_db_usergroup_permissions done. \n";
            
            $this->updateReference('_db_menus', 'parent_id', '_db_menus', 'id', 'label');
            echo "_db_menus done. \n";

            $this->updateReference('_db_menu_permissions', 'menu_id', '_db_menus', 'id', 'label');
            $this->updateReference('_db_menu_permissions', 'usergroup_id', 'usergroups', 'id', 'group');
            echo "_db_menu_permissions done. \n";
            
            $this->updateReference('medias', 'gallery_id', 'galleries', 'id', 'name');
            $this->updateReference('medias', 'mcollection_id', 'mcollections', 'id', 'name');
            $this->updateReference('medias', 'user_id', 'users', 'id', 'username');
            echo "medias done. \n";

            $this->updateReference('galleries', 'media_id', 'medias', 'id', 'file_name');
            echo "galleries done. \n";
            
            $this->updateReference('_db_options', 'option_type_id', '_db_option_types', 'id', 'name');
            echo "_db_options done. \n";
            
            $this->updateReference('_db_option_types', 'parent_id', '_db_option_types', 'id', 'name');
            echo "_db_option_types done. \n";
            
            $this->updateReference('_db_key_fields', 'pk_field_id', '_db_fields', 'id', 'fullname');
            $this->updateReference('_db_key_fields', 'pk_display_field_id', '_db_fields', 'id', 'fullname');
            $this->updateReference('_db_key_fields', 'fk_field_id', '_db_fields', 'id', 'fullname');
            $this->updateReference('_db_key_fields', 'fk_display_field_id', '_db_fields', 'id', 'fullname');
            echo "_db_key_fields done. \n";

            $this->updateReference('_db_key_fields', 'key_id', '_db_keys', 'id', 'name');
            $this->updateReference('_db_keys', 'key_type_id', '_db_key_types', 'id', 'name');
            echo "_db_key_fields done. \n";
            
            $this->updateReference('contents', 'content_type_id', 'content_types', 'id', 'name');
            echo "contents done. \n";
            
//            $this->updateReference('assets', 'asset_type_id', '_db_asset_types', 'id', 'name');
            $this->updateReference('_db_page_assets', 'page_type_id', '_db_option_types', 'id', 'name');
            $this->updateReference('_db_page_assets', 'asset_type_id', '_db_assets', 'id', 'url');
            echo "_db_page_assets done. \n";
            
            Log::write("success", "Completed foreign key references");
        }
        catch (Exception $e)
        {
            Log::write("success", "Error while inserting foreign key references.");
            Log::write("success", $e->getMessage());
            throw new Exception($e);
        }
    }

}

?>
