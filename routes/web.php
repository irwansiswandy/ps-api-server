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

/*
 * ---------------------
 * Authentication routes
 * ---------------------
*/

$router->get('activate/{activation_token}/{user_id}/{guard}', ['uses' => 'AuthController@activateAccount']);
$router->post('register', ['uses' => 'AuthController@register']);
$router->post('login', ['uses' => 'AuthController@login']);

/*
 * --------------------------
 * Users routes
 * --------------------------
 */

$router->group([
    'prefix' => 'api',
    'middleware' => 'auth:user'
], function () use ($router) {
    $router->get('/', function () use ($router) {
        return $router->request->user();
    });
});

/*
 * --------------------------
 * Development testing routes
 * --------------------------
 */
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

    $router->get('mutasi_bca/get', function () use ($router) {
        $klikbca = new App\Classes\KlikBCA();
        $data = $klikbca->mutasiSemua('2021-03-01', '2021-03-13')->get();
    
        return response([
            'message' => 'Get data from klikbca succeed',
            'data' => $data
        ]);
    });

    $router->get('mutasi_bca/save', function () use ($router) {
        $klikbca = new App\Classes\KlikBCA();
        $klikbca->mutasiSemua('2021-03-01', '2021-03-13')->save();
    
        return response([
            'message' => 'Data from klikbca saved'
        ]);
    });

    $router->get('mutasi_bca/store', function () use ($router) {
        $klikbca = new App\Classes\KlikBCA();
        $klikbca->mutasiSemua('2021-04-01', '2021-04-30')->store();

        return response([
            'message' => 'Mutasi BCA has been stored'
        ]);
    });

    $router->get('jsonq', function () use ($router) {
        $contents = file_get_contents(base_path() . '\public' . '\mutasi-bca_2021-03-01-2021-03-13.json');

        return json_decode($contents, true)[0]['description'];
    });
});