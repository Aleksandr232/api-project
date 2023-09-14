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
    public function register(Request $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

      /*   $userToken = $user->createToken('remember_token')->plainTextToken;
        $user->remember_token = $userToken; */
        $user->save();

        return response()->json(['success'=>'Регистрация прошла успешно']);
    }


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

    public function logout(Request $request) {

        Auth::logout();

        $success_message = 'Пока!' . Auth::user()->name;
        return response()->json(['success' => $success_message]);

    }

}




