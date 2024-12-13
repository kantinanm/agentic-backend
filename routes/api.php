<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', [ApiController::class, 'login']);
//Route::post('logout', [ApiController::class, 'logout']);
Route::post('register', [ApiController::class, 'register']);
// Route::get('jobs/price/{price}', [DataController::class, 'filterByPrice']);

// //Route::get('job/{id}', [DataController::class, 'getById']);
// Route::put('/jobs/{id}',[DataController::class,'update']);

Route::group(['middleware' => 'auth:api'], function(){

    Route::middleware('scope:user_profile')->get('/me', [ApiController::class, 'me']);
    Route::middleware('scope:email-notify')->post('/email-notify', [ApiController::class, 'emailNotify']);
    Route::middleware('scope:list-acceptance')->get('/list-acceptance', [ApiController::class, 'listAcceptance']);
    //Route::get('/me', [ApiController::class, 'me']);
    // Route::middleware('scope:get-job-detail')->get('job/{id}', [DataController::class, 'getById']);
    // Route::middleware('scope:find-job-date-between')->get('jobs/price/{price}/{startDate}/{endDate}', [DataController::class, 'filterByPriceAndDateBetween']);
    // Route::middleware('scope:update-job')->put('/jobs/{id}',[DataController::class,'update']);
});
