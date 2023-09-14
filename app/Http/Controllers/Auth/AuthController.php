<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;





class AuthController extends Controller
{
   /**
     * @OA\Post(
     *     path="api/register",
     *     tags={"Auth"},
     *     summary="Регистрация нового пользователя",
     *     description="Создает нового пользователя",
     *     @OA\RequestBody(
     *         description="Детали нового пользователя",
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешная операция",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="string", example="Регистрация прошла успешно")
     *         )
     *     ),
     * )
     */

    public function register(Request $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $userToken = $user->createToken('remember_token')->plainTextToken;
        $user->remember_token = $userToken;
        $user->save();

        return response()->json(['success'=>'Регистрация прошла успешно']);
    }


    /**
     * @OA\Post(
     *     path="api/login",
     *     tags={"Auth"},
     *     summary="Аутентификация пользователя",
     *     description="Аутентификация пользователя на основе email и password",
     *     @OA\RequestBody(
     *         description="Данные пользователя",
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешная операция",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Успешно вошли в систему"),
     *             @OA\Property(property="token", type="string", example="token"),
     *             @OA\Property(property="access_code", type="string", example="access_code"),
     *             @OA\Property(property="username", type="string", example="John Doe")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Ошибка аутентификации",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ошибка аутентификации")
     *         )
     *     ),
     * )
     */


    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($validatedData)) {
            $user = Auth::user();
            $userToken = $user->createToken('remember_token')->plainTextToken;
            $user->remember_token = $userToken;
            $user->save();
            $username = $user->name;
            $response = [
                'message' => 'Успешно вошли в систему',
                'token' => $userToken,
                'access_code' => $user->access_code,
                'username' => $username
            ];

            return response()->json($response, 200);
        } else {
            return response()->json(['message' => 'Ошибка аутентификации'], 403);
        }
    }

      /**
     * @OA\Get(
     *    path="api/logout",
     *     tags={"Auth"},
     *     summary="Выход из системы",
     *     description="Выход текущего аутентифицированного пользователя из системы",
     *     @OA\Response(
     *         response=200,
     *         description="Успешная операция",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="string", example="Пока! John Doe")
     *         )
     *     ),
     * )
     */

    public function logout(Request $request) {

        Auth::logout();

        $success_message = 'Пока!' . Auth::user()->name;
        return response()->json(['success' => $success_message]);

    }

}




