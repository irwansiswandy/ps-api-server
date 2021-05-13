<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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
    return response()->json([
        'name' => 'ps-api-server',
        'framework' => $router->app->version(),
        'timezone' => env('APP_TIMEZONE'),
        'date' => date('Y-m-d'),
        'time' => date('H:m:s'),
        'author' => [
            'name' => 'irwan siswandy',
            'email' => 'irwansiswandymks@gmail.com'
        ]
    ]);
});

$router->get('activate/{activation_token}/{user_id}/{guard}', ['uses' => 'AuthController@activateAccount']);
$router->post('register', ['uses' => 'AuthController@register']);
$router->post('login', ['uses' => 'AuthController@login']);

$router->group([
    'prefix' => 'api',
    'middleware' => 'auth:user'
], function () use ($router) {
    $router->get('/', function () use ($router) {
        return $router->request->user();
    });
});

$router->group([
    'prefix' => 'test'
], function () use ($router) {
    $router->get('authenticated_user', function () use ($router) {
        dd($router->app->auth->guard('api')->user());
    });
    $router->post('authentication', function () use ($router) {
        // Check if email and password are given
        $this->validate($router->app->request, [
            'email' => 'required',
            'password' => 'required'
        ]);
        dd($router->app->request->input('email'));
    });
});