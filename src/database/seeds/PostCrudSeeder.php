<?php namespace Laravella\Crud;

use Laravella\Crud\CrudSeeder;

class PostCrudSeeder extends CrudSeeder {
    
    public function run()
    {
        
        $skin = "flatly";
        
        $defaultView = "skins::$skin.dbview";
        
        // change table titles in select lists
        //crud
        $this->addPage('_db_severities', 'getSelect', null, array('title'=>'Severities'));
        $this->addPage('_db_pages', 'getSelect', null, array('title'=>'Pages'));
        $this->addPage('_db_tables', 'getSelect', null, array('title'=>'Tables'));
        $this->addPage('_db_user_permissions', 'getSelect', null, array('title'=>'User Permissions'));
        $this->addPage('_db_usergroup_permissions', 'getSelect', null, array('title'=>'Usergroup Permissions'));
        $this->addPage('_db_views', 'getSelect', null, array('title'=>'Views'));
        $this->addPage('_db_widget_types', 'getSelect', null, array('title'=>'Widget Types'));
        $this->addPage('_db_actions', 'getSelect', null, array('title'=>'Actions'));
        $this->addPage('_db_audit', 'getSelect', null, array('title'=>'Audit'));
        $this->addPage('_db_display_types', 'getSelect', null, array('title'=>'Display Types'));
        $this->addPage('_db_fields', 'getSelect', null, array('title'=>'Fields'));
        $this->addPage('_db_logs', 'getSelect', null, array('title'=>'Logs'));
        $this->addPage('_db_menu_permissions', 'getSelect', null, array('title'=>'Menu Permissions'));
        $this->addPage('_db_menus', 'getSelect', null, array('title'=>'Menus'));
        $this->addPage('_db_option_types', 'getSelect', null, array('title'=>'Option Types'));
        $this->addPage('_db_options', 'getSelect', null, array('title'=>'Options'));
        $this->addPage('_db_keys', 'getSelect', null, array('title'=>'Keys'));
        $this->addPage('_db_key_fields', 'getSelect', null, array('title'=>'Key Fields'));
        $this->addPage('_db_key_types', 'getSelect', null, array('title'=>'Key Types'));
        $this->addPage('_db_objects', 'getSelect', null, array('title'=>'Objects'));
        $this->addPage('_db_assets', 'getSelect', null, array('title'=>'Assets'));
        $this->addPage('_db_events', 'getSelect', null, array('title'=>'Events'));
        
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