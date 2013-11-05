<?php namespace Laravella\Crud\Facades;

/**
 * Description of Options facade
 *
 * @author Victor
 */

use Illuminate\Support\Facades\Facade;

class Options extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'options';
    }

}

?>
