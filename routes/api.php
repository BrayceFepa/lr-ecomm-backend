<?php

use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\FrontendController;
use App\Http\Controllers\API\JWTAuthController;
use App\Http\Controllers\API\ProductController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::controller(JWTAuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->middleware('jwtAuth');
    Route::get('/checkAuth', 'checkAuth')->middleware('jwtAuth', 'isAPIAdmin');
});

Route::controller(CategoryController::class)->group(function () {
    Route::post('/store-category', 'store')->middleware('jwtAuth', 'isAPIAdmin');
    Route::get('/view-category', 'index')->middleware('jwtAuth', 'isAPIAdmin');
    Route::get('/edit-category/{id}', 'edit')->middleware('jwtAuth', 'isAPIAdmin');
    Route::put('/update-category/{id}', 'update')->middleware('jwtAuth', 'isAPIAdmin');
    Route::delete("/delete-category/{id}", 'destroy')->middleware('jwtAuth', 'isAPIAdmin');
    Route::get("/all-category", 'allcategories')->middleware('jwtAuth', 'isAPIAdmin');
});

Route::controller(ProductController::class)->group(function () {
    Route::post("/store-product", 'store')->middleware('jwtAuth', 'isAPIAdmin');
    Route::get("/view-products", 'index')->middleware('jwtAuth', 'isAPIAdmin');
    Route::get("/edit-product/{id}", 'edit')->middleware('jwtAuth', 'isAPIAdmin');
    Route::post("/update-product/{id}", 'update')->middleware('jwtAuth', 'isAPIAdmin');
});

Route::controller(FrontendController::class)->group(function () {
    Route::get("/get-categories", "category");
    Route::get("/fetch-products/{slug}", "product");
    Route::get("/view-product/{category}/{product}", "viewProduct");
});

// Route::controller()
// Route::get('/user-profile', function () {
//     return response()->json(['message' => 'test']);
// });