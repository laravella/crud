<?php namespace Laravella\Crud;

use Laravella\Crud\CrudSeeder;

class PostCrudSeeder extends CrudSeeder {
    
    public function run()
    {
        // change table titles in select lists
        //crud
        //$this->setTitle($slug, $title);
        $this->setTitle('_db_severities_getSelect', 'Severities');
        $this->setTitle('_db_pages_getSelect', 'Pages');
        $this->setTitle('_db_tables_getSelect', 'Tables');
        $this->setTitle('_db_user_permissions_getSelect', 'User Permissions');
        $this->setTitle('_db_usergroup_permissions_getSelect', 'Usergroup Permissions');
        $this->setTitle('_db_views_getSelect', 'Views');
        $this->setTitle('_db_widget_types_getSelect', 'Widget Types');
        $this->setTitle('_db_actions_getSelect', 'Actions');
        $this->setTitle('_db_audit_getSelect', 'Audit');
        $this->setTitle('_db_display_types_getSelect', 'Display Types');
        $this->setTitle('_db_fields_getSelect', 'Fields');
        $this->setTitle('_db_logs_getSelect', 'Logs');
        $this->setTitle('_db_menu_permissions_getSelect', 'Menu Permissions');
        $this->setTitle('_db_menus_getSelect', 'Menus');
        $this->setTitle('_db_option_types_getSelect', 'Option Types');
        $this->setTitle('_db_options_getSelect', 'Options');
        $this->setTitle('_db_keys_getSelect', 'Keys');
        $this->setTitle('_db_key_fields_getSelect', 'Key Fields');
        $this->setTitle('_db_key_types_getSelect', 'Key Types');
        $this->setTitle('_db_objects_getSelect', 'Objects');
        $this->setTitle('_db_assets_getSelect', 'Assets');
        $this->setTitle('_db_events_getSelect', 'Events');
        $this->setTitle('_db_page_assets_getSelect', 'Page Assets');
        
        //cms
        $this->setTitle('medias_getSelect', 'Media');
        $this->setTitle('contents_getSelect', 'Content');
        $this->setTitle('mcollections_getSelect', 'Media Collections');
        $this->setTitle('galleries_getSelect', 'Galleries');
        $this->setTitle('users_getSelect', 'Users');
        $this->setTitle('usergroups_getSelect', 'User Groups');
        $this->setTitle('categories_getSelect', 'Categories');

        //hide fields
        $this->setDisplayType('contents.content_mime_type', 'nodisplay');
        
        $this->setWidgetType('medias', 'approved', 'input:checkbox');
        $this->setWidgetType('medias', 'publish', 'input:checkbox');

        //change field labels
        $this->setFieldTitle('contents.lang', 'Language');
        $this->updateOrInsert('_db_fields', array('fullname'=>'contents.title'), array('display_order'=>'0'));

        $ugId = $this->getId('usergroups', 'group', 'admin');
        
        $mId = $this->getId('_db_menus', 'label', 'Meta Data');
        $this->delete('_db_menu_permissions', array('usergroup_id'=>$ugId, 'menu_id'=>$mId));
        
        $mId = $this->getId('_db_menus', 'label', 'Menus');
        $this->delete('_db_menu_permissions', array('usergroup_id'=>$ugId, 'menu_id'=>$mId));
        
        $this->setWidgetType('medias', 'id', 'thumbnail');
        
        //link tables to object
//        $this->linkPageToTable('contents_getpage', 'medias');
        
        //this info will be displayed above the field in the edit screen of the admin section when the [i] info button is clicked next to the field
        $this->setFieldHelp('_db_pages.content_id', 'Link to text contents of the page.', 'The \'contents\' table might contain textual content that can be displayed on the page in addition to the records linked to the page.  Add a record in the \'contents\' table and link it here.');

        //add contents to contents table
        $contentId = $this->addContents('home', 'Cart', 'Welcome', 'Welcome');
        //$this->linkPageToTable($slug, $tableName);
        //link the above contents to a page
        //$this->linkContentToPage('home', 'contents_getpage');
        
        
        echo "Crud::PostCrudSeeder done.\n";
        
    }

}