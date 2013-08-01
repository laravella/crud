<?php

use Laravella\Crud\Log;

class SeedTables extends Seeder {

    public function run()
    {

        DB::table('_db_tables')->delete();
        DB::table('_db_fields')->delete();
        
        
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
                                $colRec['_db_table_id'] = $id;
                                $colRec['name'] = $col->Field;
                                $colRec['label'] = $this->__makeLabel($col->Field);
                                if ($col->Field == "created_at" || $col->Field == "updated_at")
                                {
                                    $colRec['display'] = 0;
                                }
                                else
                                {
                                    $colRec['display'] = 1;
                                }
                                $colRec['searchable'] = 1;
                                $colRec['display_order'] = $displayOrder++;
                                $colRec['type'] = $this->__getFieldType($col->Type);
                                $colRec['length'] = $this->__getFieldLength($col->Type);
                                $colRec['width'] = $this->__getFieldWidth($colRec['type'], $colRec['length']);
                                $colRec['widget'] = $this->__getFieldWidget($colRec['type'], $colRec['length']);
                                $colRec['null'] = $col->Null;
                                $colRec['key'] = $col->Key;
                                $colRec['default'] = $col->Default;
                                $colRec['extra'] = $col->Extra;
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
        return 100;
    }    
    
    /**
     * Try and calculate the best widget to display the field in. Define the widget in json
     */
    private function __getFieldWidget($fieldType, $fieldLength) {
        return ""; //'{widget" : "input", "attributes" : {"type" : "text"}}';
    }    
        
    
}
?>