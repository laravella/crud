<?php

/**
 * Description of DbController
 *
 * @author Victor
 */
class DbController extends BaseController {

    public function getIndex()
    {
        return View::make("crud::dbview");
    }

    public function getSelect($table = null)
    {
        return View::make("crud::dbview");
    }

    public function getTable($name = null)
    {
        die;
    }

    public function missingMethod($parameters = array())
    {
        return "Missing Method";
    }

}

?>
