<?php

class SeedTables extends Seeder {

    public function run()
    {

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
}