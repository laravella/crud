<?php class Table extends Eloquent {
    protected $tableName = "";
    private $records;
    private $tableMetaData;
    
    public static function get($tableName) {
        $table = new Table();
        $table->tableName = $tableName;
        return $table;
    }
    
    

}

?>
