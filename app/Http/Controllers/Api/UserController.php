<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Rules\Password;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:225'],
            'username' => ['required', 'string', 'max:225', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:225', 'unique:users'],
            'phone_number' => ['nullable', 'string', 'max:225', 'unique:users'],
            'password' => ['required', 'confirmed','string', new Password, 'min:8'],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ResponseFormatter::error([
                'error_messages' => $validator->messages()
            ], 'Validation Error 🗡 ', 422);
        }
        
        try {
            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'password' => Hash::make($request->password),
            ]);

            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user,
            ], 'User Berhasil registrasi 🚀 ');
        } catch (\Exception $err) {
            return ResponseFormatter::error([
                'error' => $err
            ], 'Oh No, Something went wrong 💥. Please Contact our Support', 500);
        }
    }
}
