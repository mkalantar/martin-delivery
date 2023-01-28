<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer;
use App\Http\Controllers\Courier;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::controller(Customer::class)->group(function () {

  Route::post('/request', 'createRequest');
  Route::delete('/request/{id}', 'cancelRequest');
});

Route::controller(Courier::class)->group(function () {
  Route::get('/request', 'availableRequests');
});
