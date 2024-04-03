<?php

namespace App\Http\Controllers\Employee\Auth;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class EmployeeAuthController extends Controller
{
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

        $user = Employee::where('email', $request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'data' => [],
                'message' => 'Invalid credentials',
                'status' => 401,
                ], 401);
        }

        $data['token'] = $user->createToken('employee-token', ['AsEmployee'])->plainTextToken;
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
