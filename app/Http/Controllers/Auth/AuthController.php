<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;





class AuthController extends Controller
{
/**
 * @OA\Post(
 *     path="api/register",
 *     summary="Зарегистрироваться",
 *     tags={"Авторизация и регистрация пользователя"},
 *     @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property="name",
 *                     type="string",
 *                     example="John Doe",
 *                     description="Имя пользователя"
 *                 ),
 *                 @OA\Property(
 *                     property="email",
 *                     type="string",
 *                     example="john.doe@example.com",
 *                     description="Email пользователя"
 *                 ),
 *                 @OA\Property(
 *                     property="password",
 *                     type="string",
 *                     example="password",
 *                     description="Пароль пользователя"
 *                 ),
 *                 @OA\Property(
 *                     property="img",
 *                     example="img",
 *                     type="file",
 *                     description="Фото пользователя"
 *                 ),
 *             ),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Успешная регистрация",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="success",
 *                 type="string",
 *                 example="Регистрация прошла успешно"
 *             ),
 *         ),
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

        if($request->hasFile('img')){
            $img = $request->file('img');
            $path = Storage::disk('user')->putFile('photo', $img);

            $user->img = $img->getClientOriginalName();
            $user->path = $path;
            $userToken = $user->createToken('remember_token')->plainTextToken;
            $user->remember_token = $userToken;
            $user->save();
        }

        return response()->json(['success'=>'Регистрация прошла успешно']);
    }


    /**
     * @OA\Post(
     *     path="api/login",
     *     tags={"Авторизация и регистрация пользователя"},
     *     summary="Аутентификация пользователя",
     *     description="Аутентификация пользователя на основе email и password и токена",
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
             $userToken = $user->remember_token;

             if (empty($userToken)) {
                 $userToken = $user->createToken('remember_token')->plainTextToken;
                 $user->remember_token = $userToken;
                 $user->save();
             }

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
     *     path="api/dashboard",
     *     tags={"Панель управления"},
     *     summary="Получить панель управления пользователя",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Успешный ответ с именем пользователя",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="Приветственное сообщение с именем пользователя",
     *                 example="Добро пожаловать, Пользователь"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Ошибка авторизации",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="Сообщение об ошибке, объясняющее неавторизованный доступ",
     *                 example="Неавторизованный доступ"
     *             )
     *         )
     *     )
     * )
     */

     public function dashboard(Request $request)
     {
        $user = $request->user();

        if (!empty($user->remember_token)) {
            return response()->json(['message' => 'Добро пожаловать, ' . $user->name]);
        } else {
            abort(401, 'Неавторизованный доступ');
        }
     }

      /**
     * @OA\Post(
     *    path="api/logout",
     *     tags={"Панель управления"},
     *     summary="Выход из системы",
     *     description="Выход текущего аутентифицированного пользователя из системы",
     *     @OA\Response(
     *         response=200,
     *         description="Успешная операция",
     *         @OA\JsonContent(
     *             @OA\Property(property="success_message", type="string", example="Пока! John Doe")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Пользователь не найден",
     *         @OA\JsonContent(
     *             @OA\Property(property="error_message", type="string", example="Пользователь не найден")
     *         )
     *     ),
     * )
     */

    public function logout(Request $request) {
        $user = $request->user();

        if (!empty($user->remember_token)) {
            $user->remember_token = null;
            $user->save();
                return response()->json(['success_message' => 'Пока, ' . $user->name]);
        } else {
            	return response()->json(['error_message' => 'Пользователь не найден!']);
        }

    }

}




