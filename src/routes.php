<?php

Route::filter('frontfilter', function($route)
{
    if (Request::segment(1) == 'en' && Request::segment(2) == 'list')
    {
        
    } else {
        return Redirect::to('/account/login');
    }
});

Route::filter('crudauth', function()
        {
            if (Request::ajax())
            {
                if (Auth::guest())
                {
                    App::abort(403);
                }
            }

            if (Auth::guest())
                return Redirect::to('/account/login');
        });
Route::get('admin', function()
        {
            Auth::logout();
            return Redirect::to('/db/select/contents');
        });
        
Route::when('db/*', 'crudauth');
Route::when('pg/*', 'crudauth');
Route::when('en/*', 'frontfilter'); //guest use i.e. front end
Route::when('dbapi/*', 'crudauth');
//Route::when('dbinstall/*', 'crudauth');

Route::controller('db', 'DbController');
Route::controller('en', 'DbController');
Route::controller('pg', 'DbController');
Route::controller('dbapi', 'DbApiController');
//Route::controller('dbinstall', 'DbInstallController');

?>
