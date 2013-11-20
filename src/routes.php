<?php

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
    {
        return Redirect::to('/account/login');
    }
});

Route::when('db/*', 'crudauth');
Route::when('pg/*', 'crudauth');
Route::when('dbapi/*', 'crudauth');
Route::when('admin', 'crudauth');

Route::get('/query', function()
{
    $get = Input::get('q');
    if (!empty($get))
    {
        return Redirect::to("/search/$get");
    }
});

Route::get('/search/{searchPhrase}', 'SearchController@search');

/**
 * All pages go through this route, except the home page
 * Slug is a machine friendly version of the page title e.g. The Page Title = the-page-title
 */
//Route::controller('/page/{pageSlug}', 'PageController');
//
//Route::controller('/media/{mcollection}', 'MediaController');
//
//Route::get('/gallery/{gallery}', 'MediaController@getGallery');

/**
 * Process Logout process
 */
Route::get('logout', function()
{
    Auth::logout();
    return Redirect::to('/');
});

Route::get('login', function()
{
    return Redirect::to('/account/login');
});

Route::get('admin', function()
{
            return Redirect::to('/db/select/contents');
});

Route::controller('db', 'DbController');
Route::controller('pg', 'DbController');
Route::controller('dbapi', 'DbApiController');
Route::controller('account', 'AccountController');
//Route::controller('/', 'PageController');



