<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api/'], function ($router) {
    $router->get('/','ExampleController@index');
    $router->post('/login','UserController@authenticate');
    $router->post('/register','UserController@register');

    $router->group(['prefix' => 'v1'], function ($router) {
        $router->get('/', 'ApiController@index');
        $router->get('/user-info', 'ApiController@userinfo');
        $router->post('/user-ava', 'ApiController@userava');
        $router->get('/user-address', 'ApiController@show_useraddress');
        $router->post('/user-address', 'ApiController@edit_useraddress');
        $router->get('/store', 'ApiController@show_store');
        $router->post('/store', 'ApiController@edit_store');
        $router->get('/product', 'ApiController@show_product');
        $router->post('/product', 'ApiController@add_product');
        $router->get('/home', 'ApiController@feed_home');
    });
});
