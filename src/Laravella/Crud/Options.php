<?php namespace Laravella\Crud;

/**
 * Description of Options
 *
 * @author Victor
 */
class Options {

    public function get($name) {
        $setting = '';
        $option = DB::table('_db_options')->where('name', $name)-first();
        if (is_object($option)) {
            $setting = $option->value;
        }
        return $setting;
    }

}

?>
