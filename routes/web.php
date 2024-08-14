<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

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
    $data = [
        'name' => config('app.name'),
        'version' => config('app.version'),
        'framework' => $router->app->version(),
        'environment' => config('app.env'),
        'debug_mode' => config('app.debug'),
        'timestamp' => Carbon::now()->toDateTimeString(),
        'timezone' => config('app.timezone'),
    ];

    return response()->json($data, Response::HTTP_OK);
});

$router->post('/auth/login', 'AuthController@store');
$router->group(['middleware' => 'auth:api', 'prefix' => '/auth'], function ($router) {
    $router->get('/data', 'AuthController@show');
    $router->put('/refresh-token', 'AuthController@update');
    $router->delete('/invalid-token', 'AuthController@destroy');

});

$router->group(['middleware' => 'auth:api'], function ($router) {
    $router->get('/users', 'UserController@index');
    $router->post('/users', 'UserController@store');
    $router->get('/users/{uuid}', 'UserController@show');
    $router->post('/users/{uuid}', 'UserController@update');
    $router->delete('/users/{uuid}', 'UserController@destroy');
    $router->get('/users/{uuid}/roles', 'UserController@getUserRoles');
    $router->post('/users/{uuid}/roles', 'UserController@syncUserRoles');
    $router->get('/users/profile/get', 'UserController@getProfile');

    $router->get('/permissions', 'PermissionController@index');
    $router->post('/permissions', 'PermissionController@store');
    $router->get('/permissions/{uuid}', 'PermissionController@showById');
    $router->get('/permissions/name/{name}', 'PermissionController@showByName');
    $router->put('/permissions/{uuid}', 'PermissionController@update');
    $router->delete('/permissions/{uuid}', 'PermissionController@destroy');

    $router->get('/roles', 'RoleController@index');
    $router->post('/roles', 'RoleController@store');
    $router->get('/roles/{uuid}', 'RoleController@showById');
    $router->get('/roles/name/{name}', 'RoleController@showByName');
    $router->put('/roles/{uuid}', 'RoleController@update');
    $router->delete('/roles/{uuid}', 'RoleController@destroy');
    $router->get('/roles/{uuid}/permissions', 'RoleController@getRolePermissions');
    $router->post('/roles/{uuid}/permissions', 'RoleController@syncRolePermissions');
});
