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

/*$app->get('/', function () use ($app) {
	var_dump($request); exit;
    return $app->version();
});*/

$app->group(['prefix' => 'flgs'], function () use ($app) {
	$app->post('/', 'FLGSController@store');
});
// $app->get('/', 'FLGSController@test');

$app->get('401', function () use ($app) {
	return response()->json(['error' => 'Not logged in']);
});

$app->get('403', function () use ($app) {
	return response()->json(['error' => 'Unauthorized']);
});

$app->get('404', function () use ($app) {
	return response()->json(['error' => 'Page not found']);
});
