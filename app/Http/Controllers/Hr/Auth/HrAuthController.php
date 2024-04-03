<?php

namespace App\Http\Controllers\Hr\Auth;

use App\Http\Controllers\Controller;
use App\Models\Hr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class HrAuthController extends Controller
{
    public function register(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:250',
            'email' => 'required|string|email:rfc,dns|max:250|unique:hrs,email',
            'password' => 'required|string|min:8|confirmed'
        ]);

        if($validate->fails()){
            return response()->json([
                'data' => $validate->errors(),
                'message' => 'Validation Error!',
                'status' => 403,
            ], 403);
        }

        $user = Hr::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $data['token'] = $user->createToken('hr-token', ['AsHr'])->plainTextToken;
        $data['user'] = $user;

        $response = [
            'data' => $data,
            'message' => 'User is created successfully.',
            'status' => 201,
        ];

        return response()->json($response, 201);
    }

    public function login(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        if($validate->fails()){
            return response()->json([
                'data' => $validate->errors(),
                'message' => 'Validation Error!',
                'status' => 403,
            ], 403);  
        }

        $user = Hr::where('email', $request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'data' => [],
                'message' => 'Invalid credentials',
                'status' => 401,
                ], 401);
        }

        $data['token'] = $user->createToken('hr-token', ['AsHr'])->plainTextToken;
        $data['user'] = $user;
        
        $response = [
            'data' => $data,
            'message' => 'User is logged in successfully.',
            'status' => 200,
        ];

        return response()->json($response, 200);
    } 

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'data' => [],
            'message' => 'User is logged out successfully',
            'status' => 200,
            ], 200);
    }
}
