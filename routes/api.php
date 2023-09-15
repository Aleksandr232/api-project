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
/* Route::get('/logout', [AuthController::class, 'logout']); */

// Запрещаем переход на другую страницу, если токен не прошел
/* Route::middleware('auth:sanctum')->get('/dashboard', function (Request $request) {
    if ($request->user()) {
        return response()->json(['message' => 'Добро пожаловать']);
    } else {
        abort(401, 'Unauthorized');
    }
}); */

Route::middleware('auth:sanctum')->get('/dashboard', function (Request $request) {
    $user = $request->user();

    if (!empty($user->remember_token)) {
        return response()->json(['message' => 'Добро пожаловать, ' . $user->name]);
    } else {
        abort(401, 'Unauthorized');
    }
});

Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
    $user = $request->user();

    if (!empty($user->remember_token)) {
        $user->remember_token = null;
        $user->save();
        return response()->json(['message' => 'Вы успешно вышли из системы']);
    } else {
        abort(401, 'Unauthorized');
    }
});




