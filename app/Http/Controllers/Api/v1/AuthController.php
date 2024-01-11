<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Login using request
     * POST: /api/login
     * @param Request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "password" => "required"
        ]);

        if($validator->fails()){
            return response()->json([
                "ok" => false,
                "errors" => $validator->errors(),
                "message" => "Failed to pass the validation!"
            ], 400);
        }

        if(!auth()->attempt($validator->validated())){
            return response()->json([
                "ok" => false,
                "message" => "Invalid Credentials!"
            ], 401);
        }

        $token = auth()->user()->createToken('MFI_API')->accessToken;

        return response()->json([
            "ok" => true,
            "data" => auth()->user(),
            "token" => $token,
            "message" => "Logged in successfully"
        ], 200);
    }

    /**
     * Register using request
     * POST: /api/register
     * @param Request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            "name" => "required|unique:users|alpha_dash|min:4|max:32",
            "password" => "required|min:8|max:64"
        ]);

        if($validator->fails()){
            return response()->json([
                "ok" => false,
                "errors" => $validator->errors(),
                "message" => "Failed to pass the validation!"
            ], 400);
        }
        $validated = $validator->validated();
        $validated['password'] = bcrypt($validated['password']);

        $user = User::create($validated);
        $token = $user->createToken('MFI_API')->accessToken;

        return response()->json([
            "ok" => true,
            "data" => $user,
            "token" => $token,
            "message" => "Logged in successfully"
        ], 200);
    }
}
