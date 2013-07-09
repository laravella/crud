<?php

//create a named route
//Route::get('/db/'.$table, array('as' => 'db.'.$table, 'uses' => 'DbController@getIndex'));

Route::get('/db', array('before' => 'auth.basic', function() {
    echo "<a href=\"/db/api/asdf\">Greeting</a><br />";
    echo "<a href=\"/db/table\">Table</a><br />";
}));

Route::get('/db/table', array('before' => 'auth.basic', function() {
    echo "Use : /db/table/&lt;tablename&gt;";
}));

Route::get('/db/api', array('before' => 'auth.basic', function() {
    echo "Use : /db/api/&lt;apicall&gt;";
}));

Route::get('/db/table/{table}', array('before' => 'auth.basic', 'uses' => 'DbController@getIndex'));

Route::get('/db/api/{call}', array('before' => 'auth.basic', function() {
    echo DbGopher::greeting();
}));

?>
