<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserSignupRequest;
use App\Http\Requests\UserLoginRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends BaseController
{
    public function register(UserSignupRequest $request)
    {
        $user =  User::create($request->all());
        $token = $user->createToken('demoApps')->plainTextToken;
        $data['access_token'] = $token;

        return $this->sendResponse($data, 'User successfully register');
    }


    public function login(UserLoginRequest $request)
    {
        $attributes = $request->except('_token');
        if (!Auth::attempt($attributes)) {
            return $this->sendError("", 'Invalid credentials', 401);
        }
        $data['access_token'] = auth()->user()->createToken('demoApps')->plainTextToken;
        return $this->sendResponse($data,'User successfully logged in');
    }

    public function logout(){
        auth()->user()->currentAccessToken()->delete();
        return $this->sendResponse("",'User successfully logged out');

    }
}
