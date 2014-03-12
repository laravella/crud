<?php

namespace Laravella\Crud;

use \DB;
use Laravella\Crud\Exceptions\DBException;

/**
 * Description of DbGopher
 *
 * @author Victor
 */
class DbGopher {

    public static function backtrace() {
        $bt = debug_backtrace();
        $arr = array();
        $i = 0;
        foreach($bt as $btx) {
            if(isset($btx['file'])) {  
                echo $btx['file']." : ".$btx['line']."\n";
            }
            
            /*
            $i++;
            $arr[] = array("file"=>$btx['file'], "line"=>$btx['line']);
            if ($i == 10) {
                return $arr;
                exit();
            }
             * 
             */
        }
        return array();
    }
    
    /**
     * Turn a StdClass object into an array using an array of meta data objects.
     * 
     * @param type $meta An array of stdClass objects, each object representing a field's metadata (_db_fields). 
     *  You can use Table::getMeta($tableName) or Table::getTableMeta($tableName)['fields'] to get this.
     * @param type $data An array of stdClass objects, each object a record. (the result of DB::table('tableName')->get() not ->first() )
     */
    public static function makeArray($meta, $data)
    {

        $pkName = "";
        $arr = array();

        //loop through records
        foreach ($data as $rec)
        {

            $recA = array();
            //for each fieldname in metadata
            foreach ($meta as $metaField)
            {
                //find the name of the primary key so that we can index the array according to that field's values
                if ($metaField->key == 'PRI')
                {
                    $pkName = $metaField->name;
                }
                //get field name
                $fieldName = $metaField->name;
                //populate array with value of field
                if (property_exists($rec, $fieldName))
                {
                    $recA[$fieldName] = $rec->$fieldName;
                }
            }
            //add record array to table array
            $arr[] = $recA;
        }

        return $arr;
    }

    /**
     * Turn a StdClass object into an array using an array of meta data arrays.
     * 
     * @param type $meta An array of arrays, each one representing a field's metadata (_db_fields)
     * @param type $data An array of stdClass objects, each object a record
     */
    public static function makeArrayA($metaA, $data)
    {
        $arr = array();
        //loop through records
        foreach ($data as $rec)
        {
            $recA = array();
            //for each fieldname in metadata
            foreach ($metaA as $metaField)
            {
                //get field name
                $fieldName = $metaField['name'];
                //populate array with value of field
                if (property_exists($rec, $fieldName))
                {
                    $recA[$fieldName] = $rec->$fieldName;
                }
            }
            //add record array to table array
            $arr[] = $recA;
        }
        return $arr;
    }

    /**
     * Synonymn for pick
     */
    public static function coalesce($result, $fieldName, $default = null) {
        return self::pick($result, $fieldName, $default);
    }
    
    /**
     * check if a StdObj exists as an object and then returns a field from it.
     */
    public static function pick($result, $fieldName, $default = null)
    {
        $value = $default;
        if (is_object($result) && property_exists($result, $fieldName))
        {
            $value = $result->$fieldName;
        }
        else if (is_object($result) && property_exists($result, $fieldName))
        {
            $value = $result->$fieldName;
        }
        else
        {
            //throw new DBException('Empty record.');
        }
        return $value;
    }

    /**
     * Return last executed query
     */
    public static function getLastQuery()
    {
        $queries = DB::getQueryLog();
        $lastQuery = end($queries);
        return $lastQuery;
    }

    /**
     * get the last part of a skin name
     */
    public static function getSimpleSkinName($skinName) {
        $skinA = explode("::",$skinName);
        $i = count($skinA);
        $name = $skinName;
        if ($i == 2) {
            $name = $skinA[0]."::".$skinA[1];
        } else if ($i == 3) {
            $name = $skinA[1]."::".$skinA[2];
        }
        return $name;
    }    
}

?>
