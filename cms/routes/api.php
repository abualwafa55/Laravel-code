<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

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
/*
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
*/
Route::get('/posts', 'App\Http\Controllers\ApiController@getPosts');


/*
Route::middleware('auth:api')->group(function () {
    // Define API routes here
    Route::get('/posts', 'ApiController@getPosts');
    Route::get('/pages', 'ApiController@getPages');
    // Add more routes as needed
});
*/