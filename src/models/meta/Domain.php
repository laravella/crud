<?php class Domain {
    
    //a collection of tables as listed in _db_tables
    private $tables = array();

    /**
     * Create the _db_tables table
     * 
     * @param type $table
     */
    private static function __create__db_tables($tableName)
    {
        Schema::create($tableName, function ($table)
                {
                    $table->increments('id')->unique();
                    $table->string('name', 100)->unique();
                    $table->timestamps();                    
                    
                });
    }

    /**
     * Create the _db_fields table
     * 
     * @param type $table
     */
    private function __create__db_fields($tableName)
    {
        Schema::create($tableName, function ($table)
                {
                    $table->increments('id')->unique();
                    $table->string('name', 100);                        // the field's name
                    $table->string('label', 100);                       // the label
                    $table->integer('display')->nullable();             // the field will be displayed in lists/selects
                    $table->integer('searchable')->nullable();          // 1 if the field is display in a search form, else 0
                    $table->integer('display_order')->nullable();       // the order in which field will be displayed in lists/selects
                    $table->string('type', 100)->nullable();            // datatype
                    $table->integer('length')->nullable();              // datalength
                    $table->integer('width')->nullable();               // display width of the field in pixels
                    $table->string('widget', 250)->nullable();          // json text to define an html widget
                    $table->string('null', 3)->nullable();              // nullable
                    $table->string('key', 50)->nullable();              // type of key
                    $table->string('default', 100)->nullable();         // default value
                    $table->string('extra', 100)->nullable();
                    $table->string('href', 100)->nullable();            //hyperlink this field with the href link
                    $table->integer('_db_table_id')->unsigned();        // links to _db_tables.id
                    $table->integer('pk_field_id')->unsigned();                // links to _db_fields.id (the id of the primary key)
                    $table->integer('pk_display_field_id')->unsigned();        // links to _db_fields.id (the id of a field in the primary table that will be used as a description of the primary key id)
                    $table->timestamps();                    
                    
                    $table->unique(array('_db_table_id', 'name'));
                    $table->foreign('_db_table_id')->references('id')->on('_db_tables')->onDelete('cascade');
                });
    }

    /**
     * Create database table _db_views
     * 
     * @param type $tableName
     */
    private function __create__db_views($tableName)
    {
        Schema::create($tableName, function ($table)
                {
                    $table->increments('id')->unique();
                    $table->string('name', 100);
                    $table->timestamps();                    
                    
                });
    }

    /**
     * Create database table _db_actions
     * 
     * @param type $tableName
     */
    private function __create__db_actions($tableName)
    {
        Schema::create($tableName, function ($table)
                {
                    $table->increments('id')->unique();
                    $table->string('name', 100);
                    $table->timestamps();                    
                    
                });
    }

    /**
     * You can assign a view to a uri so that /db/edit/users can have a different view than /db/edit/accounts
     * 
     * 
     * @param type $tableName
     */
    private function __create__db_table_action_views($tableName)
    {
        Schema::create($tableName, function ($table)
                {
                    $table->increments('id');
                    $table->integer('table_id')->unsigned();
                    $table->integer('action_id')->unsigned();
                    $table->integer('view_id')->unsigned();
                    $table->timestamps();                    

                    $table->foreign('view_id')->references('id')->on('_db_views')->onDelete('cascade');
                    $table->foreign('table_id')->references('id')->on('_db_tables')->onDelete('cascade');
                    $table->foreign('action_id')->references('id')->on('_db_actions')->onDelete('cascade');
                });
    }

    /**
     * Create database table _db_actions
     * 
     * @param type $tableName
     */
    private function __create__db_user_permissions($tableName)
    {
        Schema::create($tableName, function ($table)
                {
                    $table->increments('id')->unique();
                    $table->integer('user_id')->unsigned();
                    $table->integer('table_id')->unsigned();
                    $table->integer('action_id')->unsigned();
                    $table->timestamps();                    
                    
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                    $table->foreign('table_id')->references('id')->on('_db_tables')->onDelete('cascade');
                    $table->foreign('action_id')->references('id')->on('_db_actions')->onDelete('cascade');
                });
    }

    /**
     * Create database table _db_actions
     * 
     * @param type $tableName
     */
    private function __create__db_usergroup_permissions($tableName)
    {
        Schema::create($tableName, function ($table)
                {
                    $table->increments('id')->unique();
                    $table->integer('usergroup_id')->unsigned();
                    $table->integer('table_id')->unsigned();
                    $table->integer('action_id')->unsigned();
                    $table->timestamps();                    
                    
                    $table->foreign('usergroup_id')->references('id')->on('usergroups')->onDelete('cascade');
                    $table->foreign('table_id')->references('id')->on('_db_tables')->onDelete('cascade');
                    $table->foreign('action_id')->references('id')->on('_db_actions')->onDelete('cascade');
                });
    }

    /**
     * Create a database table. To use this function there also needs to be a "__create_".$tableName function in this class.
     * 
     * @param type $tableName
     * @param type $log
     * @throws Exception
     */
    private function __create($tableName, &$log)
    {
        if (!Schema::hasTable($tableName))
        {
            try
            {
//$createName is the name of the function which contains the create declarations of the fieldnames
                $createName = "__create_" . $tableName;
                $this->$createName($tableName);
                $log[] = "Table $tableName created";
            }
            catch (Exception $e)
            {
                $log[] = "Table $tableName could not be created.";
                $log[] = $e->getMessage();
                throw new Exception($e);
            }
        }
    }
    
    
}

?>
