<?php

/**
 * Description of generic
 *
 * @author Victor
 */
class Generic extends Eloquent {
    protected $table = "";

    public function setTable($table) {
        $this->table = $table;
    }

}

?>
