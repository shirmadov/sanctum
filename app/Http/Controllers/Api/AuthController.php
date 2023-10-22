<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MobileUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Enums\TokenAbility;
use Laravel\Sanctum\PersonalAccessToken;
use MohamedGaber\SanctumRefreshToken;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $request['password'] = Hash::make($request['password']);
        $request['remember_token'] = Str::random(10);
        $user = User::create($request->toArray());

        return response()->json([
            'status' => true,
            'message' => 'User Created Successfully',
            'token' => $user->createAuthToken('auth', now()->addMinutes(2))->plainTextToken,
            'refresh_token' => $user->createRefreshToken('refresh', now()->addMinutes(3))->plainTextToken,
        ], 200);
    }

    public function refresh(Request $request){
        $token = PersonalAccessToken::findToken($request->token);
        if($token->expires_at->isPast()){
            return response()->json([
                'status' => false,
                'message' => 'Token expired',
            ], 401);
        }
        $mobile_user = MobileUser::where('id',$token->tokenable_id)->first();
        return response()->json([
            'status' => true,
            'message' => 'User Created Successfully',
            'token' => $mobile_user->createAuthToken('auth',now()->addMinutes(2))->plainTextToken,
            'refresh_token' => $mobile_user->createRefreshToken('refresh',now()->addMinutes(3))->plainTextToken,
        ], 200);
    }

    public function loginUser(Request $request)
    {
        $validateUser = Validator::make($request->all(),
            [
                'email' => 'required|email',
                'password' => 'required'
            ]);

        if(!Auth::attempt($request->only(['email', 'password']))){
            return response()->json([
                'status' => false,
                'message' => 'Email & Password does not match with our record.',
            ], 401);
        }

        $user = User::where('email', $request->email)->first();
        return response()->json([
            'status' => true,
            'message' => 'User Created Successfully',
            'token' => $user->createAuthToken('auth',now()->addMinutes(2))->plainTextToken,
            'refresh_token' => $user->createRefreshToken('refresh',now()->addMinutes(3))->plainTextToken,
        ], 200);
    }
}
