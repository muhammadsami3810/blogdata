<?php

namespace App\Http\Controllers;

use App\Models\User;
use GuzzleHttp\Psr7\Message;
use Illuminate\Http\Request;
use Laravel\Sanctum\Sanctum;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $token = $user->createToken('mytoken')->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    protected function logout(Request $request)
    {
        if ($token = $request->bearerToken()) {
            $model = Sanctum::$personalAccessTokenModel;
            $accessToken = $model::findToken($token);
            $accessToken->delete();
        }
        
        return response(['message'=>'Successfully logged out']);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response(['message' => 'Invalid credentials'], 401);
        }else{
            $token = $user->createToken('mytoken')->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token,
        ], 200);
        }
     
    }
}
