<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;

class AuthController extends Controller
{
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function register(Request $request)
    {

        $this->validate($request, [
            'name' => 'required|string|min:2|max:255',
            'email' => 'required|string|email:rfc,dns|max:255|unique:users',
            'password' => 'required|string|min:6|max:255',
        ]);


        $user = $this->user::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => bcrypt($request['password']),
        ]);

        $token = auth()->setTTL(5)->login($user);

        // return the response as json 
        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => true,
                'message' => 'User created successfully!',
            ],
            'data' => [
                'user' => $user,
                'access_token' => [
                    'token' => $token,
                    'type' => 'Bearer',
                    'expires_in' => auth()->factory()->getTTL() * 60 , 
                ],
                
            ],
        ]);
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $token = auth()->setTTL(5)->attempt([
            'email' => $request->email,
            'password' => $request->password,
        ]);

        if ($token)
        {

            return response()->json([
                'meta' => [
                    'code' => 200,
                    'status' => true,
                    'message' => 'Fetched successfully.',
                ],
                'data' => [
                    'user' => auth()->user(),
                    'access_token' => [
                        'token' => $token,
                        'type' => 'Bearer',
                        'expires_in' => auth()->factory()->getTTL() * 60 * 24,
                    ],
                ],
            ]);
        }else{
            return response()->json([
                'meta' => [
                    'code' => 200,
                    'status' => false,
                    'message' => 'Login Failed. Please Check Your Email an password',
                ],
                'data' => [],
            ]);
        }
    }

    public function logout()
    {
        $invalidate = auth()->invalidate();
        if($invalidate) {
            return response()->json([
                'meta' => [
                    'code' => 200,
                    'status' => true,
                    'message' => 'Successfully logged out',
                ],
                'data' => [],
            ]);
        }
    }
}
