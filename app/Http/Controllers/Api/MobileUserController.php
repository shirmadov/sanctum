<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MobileUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;

class MobileUserController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|max:25',
            'verify_code' => 'required|max:6',
        ]);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $mobile_user = MobileUser::create($request->toArray());

        return response()->json([
            'status' => true,
            'message' => 'User Created Successfully',
            'token' => $mobile_user->createAuthToken('auth', now()->addMinutes(2))->plainTextToken,
            'refresh_token' => $mobile_user->createRefreshToken('refresh', now()->addMinutes(3))->plainTextToken,
        ], 200);
    }

    public function loginUser(Request $request)
    {
        $validateUser = Validator::make($request->all(),
            [
                'phone' => 'required',
                'verify_code' => 'required'
            ]);

        $mobile_user = MobileUser::where([
            ['phone','=', $request->phone],
            ['verify_code','=', $request->verify_code]
        ])->first();
        if(is_null($mobile_user)){
            return response()->json([
                'status' => false,
                'message' => 'Phone & Code does not match with our record.',
            ], 401);
        }


        return response()->json([
            'status' => true,
            'message' => 'User Created Successfully',
            'token' => $mobile_user->createAuthToken('auth',now()->addMinutes(2))->plainTextToken,
            'refresh_token' => $mobile_user->createRefreshToken('refresh',now()->addMinutes(3))->plainTextToken,
        ], 200);
    }

    public function refresh(Request $request)
    {
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
}
