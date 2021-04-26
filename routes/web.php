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
