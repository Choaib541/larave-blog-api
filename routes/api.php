<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{PostController, CategoryController, UserController, RoleController, VisitorController};

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource("posts", PostController::class);

Route::apiResource("categories", CategoryController::class);

Route::apiResource("users", UserController::class);

Route::apiResource("roles", RoleController::class);

Route::apiResource("visitors", VisitorController::class)->except([
    "update"
]);
