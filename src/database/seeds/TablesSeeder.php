<?php namespace Laravella\Crud;

use Laravella\Crud\Log;
use \Seeder;
use \DB;

class SeedTables extends Seeder {

    private function __addWidgetType($name)
    {
        $widgetTypeId = DB::table('_db_widget_types')->insertGetId(array('name' => $name));
        Log::write(Log::INFO, $name . ' widget type created');
        return $widgetTypeId;
    }

    private function __addWidgetTypes()
    {
        $wTypes = array();
        
        DB::table('_db_widget_types')->delete();

        $wTypes['input:text']       = $this->__addWidgetType('input:text');
        $wTypes['input:hidden']     = $this->__addWidgetType('input:hidden');
        $wTypes['input:text']       = $this->__addWidgetType('input:text');
        $wTypes['input:checkbox']   = $this->__addWidgetType('input:checkbox');
        $wTypes['input:radio']      = $this->__addWidgetType('input:radio');
        $wTypes['textarea']         = $this->__addWidgetType('textarea');
        $wTypes['select']           = $this->__addWidgetType('select');
        $wTypes['multiselect']      = $this->__addWidgetType('multiselect');
        $wTypes['ckeditor']         = $this->__addWidgetType('ckeditor');
        $wTypes['span']             = $this->__addWidgetType('span');
        $wTypes['password']         = $this->__addWidgetType('password');
        $wTypes['password:hashed']  = $this->__addWidgetType('password:hashed');
        $wTypes['password:md5']     = $this->__addWidgetType('password:md5');
        $wTypes['thumbnail']        = $this->__addWidgetType('thumbnail');
    }    
    
   private function __addDisplayType($name)
    {
        $displayTypes = array('name' => $name);
        $displayTypeId = DB::table('_db_display_types')->insertGetId($displayTypes);
        Log::write(Log::INFO, $name . ' display types created');
        return $displayTypeId;
    }

    private function __addDisplayTypes()
    {
        $types = array();
        DB::table('_db_display_types')->delete();

        /**
         * force 0 for nodisplay (possibly obsolete)
         */
        $types['nodisplay'] = DB::table('_db_display_types')->insertGetId(array('id'=>0, 'name'=>'nodisplay'));

        $types['edit'] = $this->__addDisplayType('edit');
        $types['display'] = $this->__addDisplayType('display');
        $types['hidden'] = $this->__addDisplayType('hidden');
        $types['link'] = $this->__addDisplayType('link');
        $types['widget'] = $this->__addDisplayType('widget');
        $types['thumbnail'] = $this->__addDisplayType('thumbnail');
        
        return $types;
    }    
    
    private function __getDisplayType($colRec, $types) {
        
        $displayTypeId = $types['edit'];
        
        if ($colRec['name'] == "created_at" || $colRec['name'] == "updated_at")
        {
            $displayTypeId = $types['nodisplay'];
        }
        return $displayTypeId;
    }
    
    public function run()
    {

        DB::table('_db_tables')->delete();
        DB::table('_db_fields')->delete();
        
        $displayTypes = $this->__addDisplayTypes();
        
        $widgetTypes = $this->__addWidgetTypes();
        
//get the list of tables from the database metadata
        $tables = DB::select('show tables');
//loop through records, each record has a tablename
        foreach ($tables as $table)
        {
            try
            {
//there is only one field, get it
                foreach ($table as $tableName)
                {
//insert it into _db_tables
                    $id = DB::table('_db_tables')->insertGetId(array('name' => $tableName));
                    Log::write("success", "Added $tableName to _db_table with id $id");
                    try
                    {
//get columns from database
                        $cols = DB::select("show columns from $tableName");
//loop through list of columns
                        $displayOrder = 0;
                        foreach ($cols as $col)
                        {
                            try
                            {
// the fields that will go into _db_fields
                                $colRec = array();
                                $colRec['table_id'] = $id;
                                $colRec['name'] = $col->Field;
                                $colRec['fullname'] = $tableName.".".$col->Field;
                                $colRec['label'] = $this->__makeLabel($col->Field);
                                $colRec['searchable'] = 1;
                                $colRec['display_order'] = $displayOrder++;
                                $colRec['type'] = $this->__getFieldType($col->Type);
                                $colRec['length'] = $this->__getFieldLength($col->Type);
                                $colRec['width'] = $this->__getFieldWidth($colRec['type'], $colRec['length']);
                                $colRec['widget_type_id'] = $this->__getFieldWidget($colRec['type'], $colRec['length']);
                                $colRec['null'] = $col->Null;
                                $colRec['key'] = $col->Key;
                                $colRec['default'] = $col->Default;
                                $colRec['extra'] = $col->Extra;
                                
                                $colRec['display_type_id'] = $this->__getDisplayType($colRec, $displayTypes);
                                        
                                $fid = DB::table('_db_fields')->insertGetId($colRec);
                                Log::write("success", " - {$colRec['name']} inserted with id $fid");
                            }
                            catch (Exception $e)
                            {
                                Log::write("important", $e->getMessage());
                                $message = " x column {$colRec['name']} could not be inserted.";
                                Log::write("important", $message);
                                throw new Exception($message, 1, $e);
                            }
                        }
                    }
                    catch (Exception $e)
                    {
                        Log::write("important", $e->getMessage());
                        $message = "Could not select columns for table $tableName";
                        Log::write("important", $message);
                        throw new Exception($message, 1, $e);
                    }
                }
            }
            catch (Exception $e)
            {
                Log::write("important", $e->getMessage());
                $message = "Error inserting table name '$tableName' into _db_tables";
                Log::write("important", $message);
                throw new Exception($message, 1, $e);
            }
        }
    }
    
    
    /**
     * Replace _ with spaces and make first character of each word uppercase
     * 
     * @param type $name
     */
    private function __makeLabel($name)
    {
        return ucwords(str_replace('_', ' ', $name));
    }

    /**
     * Returns varchar if fieldType = varchar(100) etc.
     */
    private function __getFieldType($fieldType) {
        $start = strpos($fieldType,'(');
        if ($start > 0) {
            $fieldType = substr($fieldType, 0, $start);
            Log::write("success", "fieldtype : $fieldType");
        }
        return $fieldType;
    }
    
    /**
     * Returns 100 if fieldType = varchar(100) etc.
     */
    private function __getFieldLength($fieldType) {
        $start = strpos($fieldType,'(')+1;
        $len = null;
        if ($start > 0) {
            $count = strpos($fieldType,')')-$start;
            $len = substr($fieldType, $start, $count);
            //$this->__log("success", "fieldtype : $fieldType, start : $start, count : $count, len : $len");
        }

        return $len;
    }
    
    /**
     * Try and calculate the width of the widget to display the field in 
     */
    private function __getFieldWidth($fieldType, $fieldLength) {
        return 220;
    }    
    
    /**
     * Try and calculate the best widget to display the field in. Define the widget in json
     */
    private function __getFieldWidget($fieldType, $fieldLength) {
        return ""; //'{widget" : "input", "attributes" : {"type" : "text"}}';
    }    
        
    
}
?>