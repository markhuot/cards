<?php

use App\Card;

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
Route::get('projects/join/{invite}', ['uses' => 'InviteController@join']);
Route::get('projects/{project}', ['uses' => 'ProjectController@show']);
Route::get('projects/{project}/tags', ['uses' => 'ProjectController@show']);
Route::get('projects/{project}/users', ['uses' => 'ProjectController@show']);
Route::post('projects/{project}/stacks', ['uses' => 'StackController@store']);
Route::post('projects/{project}/tags', ['uses' => 'TagController@store']);
Route::get('projects/{project}/invite', ['uses' => 'InviteController@create']);
Route::post('projects/{project}/invite', ['uses' => 'InviteController@store']);
Route::post('cards', ['uses' => 'CardController@store']);
Route::get('cards/{card}', ['uses' => 'CardController@show']);
Route::post('cards/{card}/comments', ['uses' => 'CommentController@store']);
Route::post('cards/{card}/move', ['uses' => 'CardController@move']);
Route::post('cards/{card}/check', ['uses' => 'CardController@check']);
Route::get('cards/{card}/unfollow', ['uses' => 'CardController@unfollow']);
Route::get('cards/{card}/follow', ['uses' => 'CardController@follow']);
Route::post('comments/{comment}/check', ['uses' => 'CommentController@check']);
Route::get('stacks/{stack}/cards/create', ['uses' => 'CardController@create']);
Route::get('stacks/{stack}', ['uses' => 'StackController@show']);
Route::delete('stacks/{stack}', ['uses' => 'StackController@delete']);
Route::get('tags/{tag}', ['uses' => 'TagController@show']);
Route::get('tags/{tag}/cards/create', ['uses' => 'CardController@create']);
Route::put('tags/{tag}', ['uses' => 'TagController@update']);
Route::delete('tags/{tag}', ['uses' => 'TagController@delete']);
Route::get('users/{user}', ['uses' => 'UserController@show']);
Route::get('users/{user}/cards/create', ['uses' => 'CardController@create']);

Route::get('attachments/{attachment}', ['uses' => 'AttachmentController@show']);
Route::get('attachments/{attachment}/thumb', ['uses' => 'AttachmentController@thumb']);

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
