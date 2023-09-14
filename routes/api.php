<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

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
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Запрещаем переход на другую страницу, если токен не прошел
Route::middleware('auth:sanctum')->get('/dashboard', function (Request $request) {
    if ($request->user()) {
        return response()->json(['message' => 'Добро пожаловать' . $user]);
    } else {
        abort(401, 'Unauthorized');
    }
});




