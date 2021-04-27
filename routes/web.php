<?php

use Illuminate\Support\Facades\Route;

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

  //De prueba
  Route::get( '/user/test', 'App\Http\Controllers\UserController@test' );
  Route::get( '/category/test', 'App\Http\Controllers\CategoryController@test' );
  Route::get( '/post/test', 'App\Http\Controllers\PostController@test' );

  //Oficiales para la API Rest
  Route::post( '/api/register', 'App\Http\Controllers\UserController@register' );
  Route::post( '/api/login', 'App\Http\Controllers\UserController@login' );
