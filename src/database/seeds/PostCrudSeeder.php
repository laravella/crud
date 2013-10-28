<?php namespace Laravella\Crud;

use Laravella\Crud\CrudSeeder;

class PostCrudSeeder extends CrudSeeder {
    
    public function run()
    {
        
        $defaultView = "skins::common.dbview";
        
        // change table titles in select lists
        //crud
        $this->tableActionView('_db_severities', 'getSelect', null, array('title'=>'Severities'));
        $this->tableActionView('_db_pages', 'getSelect', null, array('title'=>'Pages'));
        $this->tableActionView('_db_tables', 'getSelect', null, array('title'=>'Tables'));
        $this->tableActionView('_db_user_permissions', 'getSelect', null, array('title'=>'User Permissions'));
        $this->tableActionView('_db_usergroup_permissions', 'getSelect', null, array('title'=>'Usergroup Permissions'));
        $this->tableActionView('_db_views', 'getSelect', null, array('title'=>'Views'));
        $this->tableActionView('_db_widget_types', 'getSelect', null, array('title'=>'Widget Types'));
        $this->tableActionView('_db_actions', 'getSelect', null, array('title'=>'Actions'));
        $this->tableActionView('_db_audit', 'getSelect', null, array('title'=>'Audit'));
        $this->tableActionView('_db_display_types', 'getSelect', null, array('title'=>'Display Types'));
        $this->tableActionView('_db_fields', 'getSelect', null, array('title'=>'Fields'));
        $this->tableActionView('_db_logs', 'getSelect', null, array('title'=>'Logs'));
        $this->tableActionView('_db_menu_permissions', 'getSelect', null, array('title'=>'Menu Permissions'));
        $this->tableActionView('_db_menus', 'getSelect', null, array('title'=>'Menus'));
        $this->tableActionView('_db_option_types', 'getSelect', null, array('title'=>'Option Types'));
        $this->tableActionView('_db_options', 'getSelect', null, array('title'=>'Options'));
        $this->tableActionView('_db_keys', 'getSelect', null, array('title'=>'Keys'));
        $this->tableActionView('_db_key_fields', 'getSelect', null, array('title'=>'Key Fields'));
        $this->tableActionView('_db_key_types', 'getSelect', null, array('title'=>'Key Types'));
        $this->tableActionView('_db_objects', 'getSelect', null, array('title'=>'Objects'));
        $this->tableActionView('_db_assets', 'getSelect', null, array('title'=>'Assets'));
        $this->tableActionView('_db_events', 'getSelect', null, array('title'=>'Events'));
        
        //hide fields
        $nodisplayId = $this->getId('_db_display_types', 'name', 'nodisplay');
        $this->updateOrInsert('_db_fields', array('fullname'=>'contents.content_mime_type'), array('display_type_id'=>$nodisplayId));
        
        $widgetId = $this->getId('_db_display_types', 'name', 'widget');
        $checkboxId = $this->getId('_db_widget_types', 'name', 'input:checkbox');
        $this->updateOrInsert('_db_fields', array('fullname'=>'medias.approved'), array('display_type_id'=>$widgetId, 'widget_type_id'=>$checkboxId));
        $this->updateOrInsert('_db_fields', array('fullname'=>'medias.publish'), array('display_type_id'=>$widgetId, 'widget_type_id'=>$checkboxId));
        
        //change field titles
        $this->updateOrInsert('_db_fields', array('fullname'=>'contents.lang'), array('label'=>'Language'));
        $this->updateOrInsert('_db_fields', array('fullname'=>'contents.title'), array('display_order'=>'0'));

        $ugId = $this->getId('usergroups', 'group', 'admin');
        $mId = $this->getId('_db_menus', 'label', 'Meta Data');
        $this->delete('_db_menu_permissions', array('usergroup_id'=>$ugId, 'menu_id'=>$mId));
        $mId = $this->getId('_db_menus', 'label', 'Menus');
        $this->delete('_db_menu_permissions', array('usergroup_id'=>$ugId, 'menu_id'=>$mId));
        
        echo "Crud::PostCrudSeeder done.";
        
    }

}