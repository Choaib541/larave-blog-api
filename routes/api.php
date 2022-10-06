<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AuthController, PostController, CategoryController, UserController, RoleController, VisitorController};

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

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('posts', PostController::class)->only([
        "store",
        "update",
        "destroy",
    ]);

    Route::get('posts/own', [PostController::class, "index_own"]);

    Route::apiResource('categories', CategoryController::class)->only([
        "store",
        "update",
        "destroy",
    ])->middleware("can:admin");

    Route::apiResource("users", UserController::class)->except([
        "show"
    ]);


    Route::apiResource("roles", RoleController::class)->only([
        "store",
        "update",
        "destroy",
    ])->middleware("can:admin");

    Route::apiResource("visitors", VisitorController::class)->only([
        "index",
        "store",
        "destroy"
    ])->middleware("can:admin");

    Route::post("/logout", [AuthController::class, "logout"]);
    Route::get("/profile", [AuthController::class, "profile"]);
    Route::patch("/profile", [AuthController::class, "profile_update"]);
});


// ================================================================

Route::post("/auth", [AuthController::class, "authenticate"]);
Route::post("/register", [AuthController::class, "register"]);
Route::get("/users/{id}", [UserController::class, "show"]);
// ================================================================

Route::get("/posts", [PostController::class, "index"]);
Route::get("/posts/{id}", [PostController::class, "show"]);

// ================================================================

Route::get("/categories", [CategoryController::class, "index"]);

// ================================================================

Route::fallback(function () {
    return "Route Not Found";
});
