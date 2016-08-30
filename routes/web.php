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
Route::post('cards/{card}/comments', ['uses' => 'CommentController@store']);
Route::post('cards/{card}/move', ['uses' => 'CardController@move']);
Route::post('cards/{card}/check', ['uses' => 'CardController@check']);
Route::post('comments/{comment}/check', ['uses' => 'CommentController@check']);

// Auth::routes();
// Authentication Routes...
Route::get('login', ['uses' => 'Auth\LoginController@showLoginForm', 'name' => 'login']);
Route::post('login', ['uses' => 'Auth\LoginController@login']);
Route::post('logout', ['uses' => 'Auth\LoginController@logout']);

// Registration Routes...
Route::get('register', ['uses' => 'Auth\RegisterController@showRegistrationForm']);
Route::post('register', ['uses' => 'Auth\RegisterController@register']);
Route::get('register/{hash}', ['uses' => 'Auth\RegisterController@showRegistrationInviteForm']);
Route::post('register/{hash}', ['uses' => 'Auth\RegisterController@registerInvite']);

// Password Reset Routes...
Route::get('password/reset', ['uses' => 'Auth\ForgotPasswordController@showLinkRequestForm']);
Route::post('password/email', ['uses' => 'Auth\ForgotPasswordController@sendResetLinkEmail']);
Route::get('password/reset/{token}', ['uses' => 'Auth\ResetPasswordController@showResetForm']);
Route::post('password/reset', ['uses' => 'Auth\ResetPasswordController@reset']);

Route::get('/home', 'HomeController@index');
