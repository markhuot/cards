<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', function () { return redirect('projects'); });

Route::get('projects', ['uses' => 'ProjectController@index']);
Route::post('projects', ['uses' => 'ProjectController@store']);
Route::get('projects/join/{hash}', ['uses' => 'InviteController@join']);
Route::get('projects/{project}', ['uses' => 'ProjectController@show']);
Route::post('projects/{project}/stacks', ['uses' => 'StackController@store']);
Route::get('projects/{project}/stacks/{stack}/cards/create', ['uses' => 'CardController@create']);
Route::post('projects/{project}/stacks/{stack}/cards', ['uses' => 'CardController@store']);
Route::get('projects/{project}/invite', ['uses' => 'InviteController@create']);
Route::post('projects/{project}/invite', ['uses' => 'InviteController@store']);
Route::get('cards/{card}', ['uses' => 'CardController@show']);

Auth::routes();

Route::get('/home', 'HomeController@index');
