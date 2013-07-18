<?php

/**
 * Description of generic
 *
 * @author Victor
 */
class Model extends Eloquent {
    protected $table = "";
    
    protected $tableMeta = null;

    public function setTable($table) {
        $this->table = $table;
    }

}

?>
