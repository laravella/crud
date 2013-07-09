<?php

Route::get('/db', function() {
    echo "<a href=\"/db/api/asdf\">Greeting</a><br />";
    echo "<a href=\"/db/table\">Table</a><br />";
});

Route::get('/db/api/{call}', function() {
    echo DbGopher::greeting();
});

Route::get('/db/{table}', 'DbController@getIndex');

?>
