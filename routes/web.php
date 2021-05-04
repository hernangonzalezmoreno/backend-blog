<?php

use Illuminate\Support\Facades\Route;

use App\Http\Middleware\ApiAuthMiddleware;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//Route::get( '/prueba', 'pruebaController@test' );
//Route::get( '/prueba', '/app/Http/Controllers/pruebaController@test' );
//Route::get( '/prueba', [ PruebaController::class, 'test' ] );

Route::get( '/prueba', 'App\Http\Controllers\PruebaController@test' );

//Rutas de la API

  //--------- De prueba
  Route::get( '/user/test', 'App\Http\Controllers\UserController@test' );
  Route::get( '/category/test', 'App\Http\Controllers\CategoryController@test' );
  Route::get( '/post/test', 'App\Http\Controllers\PostController@test' );

  //--------- Oficiales para la API Rest

  // Rutas para el User
  Route::post( '/api/register', 'App\Http\Controllers\UserController@register' );
  Route::post( '/api/login', 'App\Http\Controllers\UserController@login' );
  Route::put( '/api/user/update', 'App\Http\Controllers\UserController@update' );
  Route::post( '/api/user/upload', 'App\Http\Controllers\UserController@upload' )->middleware( ApiAuthMiddleware::class );
  Route::get( '/api/user/avatar/{filename}', '\App\Http\Controllers\UserController@getImage' );
  Route::get( '/api/user/detail/{id}', '\App\Http\Controllers\UserController@detail' );

  // Rutas para la Category
  Route::resource( '/api/category', 'App\Http\Controllers\CategoryController' );

  // Rutas para los Posts
  Route:: resource( '/api/post', 'App\Http\Controllers\PostController' );
  Route::post( '/api/post/upload', 'App\Http\Controllers\PostController@upload' );
  Route::get( '/api/post/image/{filename}', 'App\Http\Controllers\PostController@getImage' );
  Route::get( '/api/post/category/{category_id}', 'App\Http\Controllers\PostController@getPostsByCategory' );
  Route::get( '/api/post/user/{user_id}', 'App\Http\Controllers\PostController@getPostsByUser' );
