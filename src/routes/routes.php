<?php
/*
Route::filter('ravelauth', function()
        {
            if (Request::ajax())
            {
                if (Auth::guest())
                {
                    App::abort(403);
                }
            }

            if (Auth::guest())
                return Redirect::action('AdminUserLoginController@getIndex');
        });
*/
Route::controller('/db/table', 'DbController');

/*
Route::group(array('prefix' => 'db'), function()
        {
        });
 * 
 */
?>
