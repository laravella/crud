<?php

namespace Laravella\Crud;

use \DB;

/**
 * Description of Options
 *
 * @author Victor
 */
class Options {

    public static function get($name, $type=null)
    {
        $setting = '';
        $option = DB::table('_db_options as o');
        if (!is_null($type) && !empty($type)) {
                $option = $option->join('_db_option_types as ot', 'ot.id', '=', 'o.option_type_id')
                ->where('ot.name', $type);
        }
        $option = $option->where('o.name', $name)
        ->select('o.value')
        ->first();
        if (is_object($option))
        {
            $setting = $option->value;
        }
        return $setting;
    }

    /**
     * 
     * @return type
     */
    public static function getSkin() {
        $skinFront = Options::get('skin', 'frontend');
        $skinAdmin = Options::get('skin', 'admin');
        $skinA = explode('::', $skinFront);
        $adminSkinA = explode('::', $skinAdmin);
        $skinVendor = 'laravella';
        $adminSkinVendor = 'laravella';

        if (count($skinA) == 3) {
            $skinVendor = $skinA[0];
            $skinPackage = $skinA[1];
            $skinName = $skinA[2];
            $skinFront = $skinPackage."::".$skinName;
                    
            $adminSkinVendor = $adminSkinA[0];
            $adminSkinPackage = $adminSkinA[1];
            $adminSkinName = $adminSkinA[2];
            $skinAdmin = $adminSkinPackage."::".$adminSkinName;
        } else if (count($skinA) == 2) {
            $skinPackage = $skinA[0];
            $skinName = $skinA[1];
            $skinFront = $skinPackage."::".$skinName;
            
            $adminSkinPackage = $adminSkinA[0];
            $adminSkinName = $adminSkinA[1];
            $skinAdmin = $adminSkinPackage."::".$adminSkinName;
        } else if (count($skinA) == 1) {
            $skinPackage = 'skins';
            $skinName = $skinA[0];
            $skinFront = $skinPackage."::".$skinName;
            
            $adminSkinPackage = 'skins';
            $adminSkinName = $adminSkinA[0];
            $skinAdmin = $adminSkinPackage."::".$adminSkinName;
        } else {
            $skinPackage = '';
            $adminSkinPackage = '';
            $skinFront = $skinPackage."::".$skinName;
            
            $skinName = '';
            $adminSkinName = '';
            $skinAdmin = $adminSkinPackage."::".$adminSkinName;
        }

        $skin = array(
            'package'=>$skinPackage, 
            'admin'=>$skinAdmin, 
            'name'=>$skinName,
            'vendor'=>$skinVendor,
            
            'adminPackage'=>$adminSkinPackage, 
            'frontend'=>$skinFront,
            'adminName'=>$adminSkinName,
            'adminVendor' => $adminSkinVendor
            );
        
        return $skin;
    }
    
    public static function getTypes($name)
    {
        $options = new Options();
        return $options->__getTypes($name);
    }

    /**
     * Get type tree
     * 
     * @param type $name
     */
    private function __getTypes($name)
    {
        if (empty($name))
        {
            return null;
        }
        $typeA = array();

        $options = DB::table('_db_option_types as pot')
                ->join('_db_option_types as cot', 'cot.parent_id', '=', 'pot.id')
                ->select('cot.name as cname', 'pot.name as pname')
                ->where('pot.name', '=', $name)
                ->get();

        if (is_array($options))
        {
            foreach ($options as $option)
            {
                $val = $this->getTypes($option->cname);
                if (!empty($val))
                {
                    $typeA[$option->cname] = $val;
                }
                else
                {
                    $typeA[$option->cname] = $option->cname;
                }
            }
        }

        return $typeA;
    }

    /**
     * 
     * @param type $types
     * @return type
     */
    private function __getValues($types)
    {
        $values = array();
        foreach ($types as $name => $type)
        {
            if (is_array($type) && count($type > 0))
            {
                $values[$name] = $this->__getValues($type);
            }
            else
            {
                $values[$name] = $this->getByType($name);
            }
        }
        return $values;
    }

    /**
     * 
     * @param type $types
     * @return type
     */
    private static function getValues($types)
    {
        $options = new Options();
        return $options->__getValues($types);
    }

    /**
     * 
     * @param type $name
     * @return type
     */
    public static function getByType($name) {
        $options = DB::table('_db_options as o')
                ->join('_db_option_types as ot', 'ot.id', '=', 'o.option_type_id')
                ->select('ot.name as type', 'o.name as option', 'o.value as value')
                ->where('ot.name', '=', $name)
                ->get();
        
        $values = array();
        foreach($options as $option) {
            $values[$option->option] = $option->value;
        }
        
        return $values;
        
    }
    
    /**
     * Get all options of a certain type
     * 
     * @param type $name
     * @return type
     */
    public static function getType($name)
    {

        $types = static::getTypes($name);

        $values = static::getValues($types);

        return $values;
    }

}

?>
