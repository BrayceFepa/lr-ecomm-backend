<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWTAuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|max:191',
                'email' => 'required|email|max:191|unique:users,email',
                'password' => 'required|min:8',
            ],
            [
                'name.required' => 'field name is required',
                'email.required' => 'email field is required',
                'email.email' => 'B, this is not a valid email',
                'password.required' => 'password field is required',
                'password.min' => 'password must be at least 8 caracters'
            ]
        );


        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'vaildator_errors' => $validator->messages(),
            ], 401);
        } else {
            $user = User::create(array_merge($validator->validate(), ['password' => bcrypt($request->password)]));

            return response()->json([
                'status' => 'success',
                'message' => 'Registered successfully !',
                'userName' => $user->name
            ], 201);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:191',
            'password' => 'required'
        ], [
            'email.required' => 'email field is required !',
            'email.email' => 'Please, enter a valid email !'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 401);
        }

        $token = null;

        if (!$token = JWTAuth::attempt($validator->validate())) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized user login attempt !'
            ], Response::HTTP_UNAUTHORIZED);
        }

        return response()->json([
            'success' => true,
            'token' => $token,
            'message' => 'Logged in successfully !',
            'user' => Auth::user()
        ]);
    }


    public function logout(Request $request)
    {
        try {
            JWTAuth::invalidate(JWTAuth::parseToken($request->token));

            return response()->json([
                'success' => true,
                'message' => 'User logged out successfully'
            ]);
        } catch (JWTException $exception) {
            return Response()->json([
                'success' => false,
                'message' => 'Sorry the user cannot be logged out'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function checkAuth()
    {
        return response()->json([
            'message' => 'You are in',
            'status' => 200
        ]);
    }










    // public function getUser(Request $request)
    // {
    //     try {
    //         $user = JWTAuth::authenticate($request->token);
    //         return response()->json(['user' => $user]);
    //     } catch (Exception $e) {
    //         return response()->json(['success' => false, 'message' => 'Something went wrong']);
    //     }
    // }



    // public function createNewToken($token)
    // {
    //     return response()->json([
    //         'message' => 'Logged in successfully !',
    //         'access_token' => $token,
    //         'token_type' => 'bearer',
    //         'expires_in' => auth()->guard('customer_api')->factory()->getTTL() * 60
    //     ], 200);
    // }
}