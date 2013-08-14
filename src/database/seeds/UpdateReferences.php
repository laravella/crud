<?php

use Laravella\Crud\Log;

class UpdateReferences extends Seeder {

    /**
     * Update a reference to primary keys in _db_fields
     * 
     * @param type $fkTableName
     * @param type $fkFieldName
     * @param type $pkTableName
     * @param type $pkFieldName
     */
    private function __updateReference($fkTableName, $fkFieldName, $pkTableName, $pkFieldName, $pkDisplayFieldName)
    {
        //get the id of the pkTableName in _db_tables
        $fkTableId = DB::table('_db_tables')->where('name', $fkTableName)->pluck('id');

        $pkTableId = DB::table('_db_tables')->where('name', $pkTableName)->pluck('id');

        //get the id of the primary key field in _db_fields
        //for each field in the _db_fields table there will thus be a reference to 
        $pkFieldId = DB::table('_db_fields')
                ->where('_db_table_id', $pkTableId)
                ->where('name', $pkFieldName)
                ->pluck('id');

        $pkDisplayFieldId = DB::table('_db_fields')
                ->where('_db_table_id', $pkTableId)
                ->where('name', $pkDisplayFieldName)
                ->pluck('id');

        $fkFieldId = DB::table('_db_fields')
                ->where('_db_table_id', $fkTableId)
                ->where('name', $fkFieldName)
                ->pluck('id');

        Log::write("success", "inserting into _db_fields : where 
            pkTableName = $pkTableName, 
            pkFieldName = $pkFieldName, 
            pkTableId = $pkTableId, 
            pkFieldId = $pkFieldId, 

            fkTableName = $fkTableName, 
            fkFieldName = $fkFieldName, 
            fkTableId = $fkTableId,
            fkFieldId = $fkFieldId");

//set the reference on the fk field
        DB::table('_db_fields')
                ->where('_db_table_id', $fkTableId)
                ->where('name', $fkFieldName)
                ->update(array('pk_field_id' => $pkFieldId, 'pk_display_field_id' => $pkDisplayFieldId));
        /*
          $this->__log("success", "updating record : {$fkRec->id}");

          DB::table('_db_fields')
          ->where('_db_table_id', $fkTableId)
          ->where('name', $fkFieldName)
          ->update(array('pk_field_id' => $fieldId));
         */
    }

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

            $this->__updateReference('_db_fields', '_db_table_id', '_db_tables', 'id', 'name');
            $this->__updateReference('_db_fields', 'pk_field_id', '_db_fields', 'id', 'fullname');
            $this->__updateReference('_db_fields', 'pk_display_field_id', '_db_fields', 'id', 'fullname');
            $this->__updateReference('_db_fields', 'display_type_id', '_db_display_types', 'id', 'name');

            $this->__updateReference('_db_table_action_views', 'view_id', '_db_views', 'id', 'name');
            $this->__updateReference('_db_table_action_views', 'table_id', '_db_tables', 'id', 'name');
            $this->__updateReference('_db_table_action_views', 'action_id', '_db_actions', 'id', 'name');

            $this->__updateReference('_db_users', 'usergroup_id', 'usergroups', 'id', 'group');
            
            $this->__updateReference('_db_user_permissions', 'user_id', 'users', 'id', 'username');
            $this->__updateReference('_db_user_permissions', 'table_id', '_db_tables', 'id', 'name');
            $this->__updateReference('_db_user_permissions', 'action_id', '_db_actions', 'id', 'name');

            $this->__updateReference('_db_usergroup_permissions', 'usergroup_id', 'groups', 'id', 'name');
            $this->__updateReference('_db_usergroup_permissions', 'table_id', '_db_tables', 'id', 'name');
            $this->__updateReference('_db_usergroup_permissions', 'action_id', '_db_actions', 'id', 'name');
            
            $this->__updateReference('_db_menus', 'parent_id', '_db_menus', 'id', 'label');

            $this->__updateReference('_db_menu_permissions', 'menu_id', '_db_menus', 'id', 'label');
            $this->__updateReference('_db_menu_permissions', 'usergroup_id', 'usergroups', 'id', 'group');
            
            $this->__updateReference('medias', 'gallery_id', 'galleries', 'id', 'name');
            $this->__updateReference('medias', 'mcollection_id', 'mcollections', 'id', 'name');
            $this->__updateReference('medias', 'user_id', 'users', 'id', 'username');
            
            $this->__updateReference('_db_options', 'option_type_id', '_db_option_types', 'id', 'name');
            $this->__updateReference('_db_option_types', 'parent_id', '_db_option_types', 'id', 'name');
            
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
