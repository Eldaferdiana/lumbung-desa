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
    return view('runup.index');
});
$router->get('/post', function () use ($router) {
    return view('runup.post');
});

$router->group(['prefix' => 'api/'], function ($router) {
    $router->get('/','ExampleController@index');
    $router->post('/login','UserController@authenticate');
    $router->post('/register','UserController@register');
    $router->post('/check','ApiController@check_msisdn');
    $router->get('/p', 'ApiController@verify_payment');

    $router->group(['prefix' => 'v1'], function ($router) {
        $router->get('/', 'ApiController@index');
        $router->get('/user-info', 'ApiController@userinfo');
        $router->get('/user-info-product', 'ApiController@userinfo_product');
        $router->post('/user-ava', 'ApiController@userava');
        $router->get('/user-address', 'ApiController@show_useraddress');
        $router->post('/user-address', 'ApiController@edit_useraddress');
        $router->get('/store', 'ApiController@show_store');
        $router->post('/store', 'ApiController@edit_store');
        $router->get('/category', 'ApiController@show_category');
        $router->get('/product', 'ApiController@show_product');
        $router->post('/product', 'ApiController@add_product');
        $router->get('/home', 'ApiController@feed_home');

        $router->get('/chat', 'ApiController@get_chat');
        $router->post('/chat', 'ApiController@chat_handle');

        $router->group(['prefix' => 'cart'], function ($router) {
            $router->get('/', 'ApiController@cart_handle');
            $router->post('/add', 'ApiController@add_to_cart');
            $router->post('/remove', 'ApiController@remove_from_cart');
            $router->post('/update', 'ApiController@update_cart');
        });

        $router->post('/checkout', 'ApiController@checkout');
        $router->post('/done', 'ApiController@set_done');
        $router->post('/accept', 'ApiController@set_accept');
        $router->post('/cencel', 'ApiController@set_cencel');
        $router->get('/history', 'ApiController@history_handle');
        $router->get('/detail', 'ApiController@detail_handle');
        $router->post('/fcm', 'ApiController@fcm_handle');
        
    });
});
