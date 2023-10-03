<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Validator;

class AuthoUserController extends Controller
{
    public function register(Request $request)
    {
        $validator=Validator::make( $request->all(),[
            'name' => 'required|string',
            'email'=>'required|string|unique:users',
            'password'=>'required|string',
            'c_password' => 'required|same:password'
        ]);
        if ($validator->fails()) {
            return response()->json(['status'=> "403",
            'message'=> "can't register",
                'error'=>$validator->errors(),
        ],403);
        }


        $user = new User([
            'name'  => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        if($user->save()){
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->plainTextToken;

            return response()->json([
            'message' => 'Successfully created user!',
            'accessToken'=> $token,
            ],201);
        }
        else{
            return response()->json(['error'=>'Provide proper details']);
        }
    }

    
public function login(Request $request)
{
    $validator=Validator::make( $request->all(),[
    'email' => 'required|string|email',
    'password' => 'required|string',
    'remember_me' => 'boolean'
    ]); 
    
    if ($validator->fails()) {
        return response()->json(['status'=> "403",
        'message'=> "validation error",
            'error'=>$validator->errors(),
    ],403);
    }

if(!Auth::attempt($request->only(['email' , 'password' ]))){
 return response()->json(
    [
        'status'=> "206",
        'message'=> "the email or the password is wrong",
           
    ]
    , 401);

}

    $credentials = request(['email','password']);
    if(!Auth::attempt($credentials))
    {
    return response()->json([
        'message' => 'Unauthorized'
    ],401);
    }

    $user = $request->user();
    $tokenResult = $user->createToken('Personal Access Token');
    $token = $tokenResult->plainTextToken;

    return response()->json([
    'accessToken' =>$token,
    'token_type' => 'Bearer',
    ]);
}
public function user(Request $request)
{
    return response()->json($request->user());
}
public function logout(Request $request)
{
    $request->user()->tokens()->delete();

    return response()->json([
    'message' => 'Successfully logged out'
    ]);

}
public function logoutonly(Request $request)
{
    $request->user()->currentAccessToken()->delete();

    return response()->json([
    'message' => 'Successfully logged out'
    ]);

}
}
