<?php

namespace Laravella\Crud;

use Laravella\Crud\Log;
use \Seeder;
use \DB;

class UpdateCMSFields extends Seeder {

    private function __updateField($tableName, $fieldName, $widgetType)
    {
        $tableId = DB::table('_db_tables')->where('name', $tableName)->pluck('id');

        //get the id of the primary key field in _db_fields
        //for each field in the _db_fields table there will thus be a reference to 
        $widgetTypeId = DB::table('_db_widget_types')
                ->where('name', $widgetType)
                ->pluck('id');
        
        //get the id of the primary key field in _db_fields
        //for each field in the _db_fields table there will thus be a reference to 
        $displayTypeId = DB::table('_db_display_types')
                ->where('name', 'widget')
                ->pluck('id');
        
        DB::table('_db_fields')
                ->where('table_id', $tableId)
                ->where('name', $fieldName)
                ->update(array('widget_type_id' => $widgetTypeId, 'display_type_id' => $displayTypeId));
    }

    public function run()
    {
        $this->__updateField('contents', 'content', 'ckeditor');
        $this->__updateField('contents', 'excerpt', 'textarea');
        $this->__updateField('medias', 'thumbnail', 'thumbnail');
    }

}

?>