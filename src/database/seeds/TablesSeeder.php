<?php namespace Laravella\Crud;

use Laravella\Crud\Log;
use \Seeder;
use \DB;

class SeedTables extends CrudSeeder {

    private function addDisplayTypes()
    {
        $types = array();
        DB::table('_db_display_types')->delete();

        /**
         * force 0 for nodisplay (possibly obsolete)
         */
        $types['nodisplay'] = $this->addDisplayType('nodisplay');
        $types['edit'] = $this->addDisplayType('edit');
        $types['display'] = $this->addDisplayType('display');
        $types['hidden'] = $this->addDisplayType('hidden');
        $types['link'] = $this->addDisplayType('link');
        $types['widget'] = $this->addDisplayType('widget');
        $types['thumbnail'] = $this->addDisplayType('thumbnail');

        return $types;
    }

    public function run()
    {

        DB::table('_db_tables')->delete();
        DB::table('_db_fields')->delete();

        $displayTypes = $this->addDisplayTypes();

//        $widgetTypes = $this->addWidgetTypes();

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
                                $colRec['fullname'] = $tableName . "." . $col->Field;
                                $colRec['label'] = $this->makeLabel($col->Field);
                                $colRec['searchable'] = 1;
                                $colRec['display_order'] = $displayOrder++;
                                $colRec['type'] = $this->getFieldType($col->Type);
                                $colRec['length'] = $this->getFieldLength($col->Type);
                                $colRec['width'] = $this->getFieldWidth($colRec['type'], $colRec['length']);
                                $colRec['widget_type_id'] = $this->getFieldWidget($colRec['type'], $colRec['length']);
                                $colRec['null'] = $col->Null;
                                $colRec['key'] = $col->Key;
                                $colRec['default'] = $col->Default;
                                $colRec['extra'] = $col->Extra;

                                $colRec['display_type_id'] = $this->getDisplayType($colRec, $displayTypes);

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

?>