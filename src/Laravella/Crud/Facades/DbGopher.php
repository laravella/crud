<?php namespace Laravella\Crud\Facades;

/**
 * Description of Db
 *
 * @author Victor
 */

use Illuminate\Support\Facades\Facade;

class DbGopher extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'dbgopher';
    }

}

?>
