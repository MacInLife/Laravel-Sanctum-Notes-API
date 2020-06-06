<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['auth:sanctum']], function (){
   //Routes notes
   Route::apiResource('/notes', 'NotesController')->only(['index', 'show', 'store']);
//    Route::get('/notes', 'NotesController@index');
//    Route::get('/notes/{id}', 'NotesController@show');
//    Route::post('/notes', 'NotesController@store');
   Route::put('/notes/{id}', 'NotesController@update');
   Route::delete('/notes/{id}', 'NotesController@destroy');
 
});
//Pas de verif Auth
Route::post('/register', 'AuthentificationController@register');
Route::post('/login', 'AuthentificationController@login');
Route::delete('/reset', 'AuthentificationController@reset');
