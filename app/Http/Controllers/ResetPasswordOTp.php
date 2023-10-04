<?php

namespace App\Http\Controllers;

use App\Models\User;
use Ichtrojan\Otp\Otp;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as RulesPassword;

class ResetPasswordOTp extends Controller
{
    private $otp;
    public function __construct(){
        $this->otp=new Otp;
    }
    public function passwordresetotp(Request $request){

        $request->validate([
            'otp' => 'required|max:6',
            'email' => 'required|email|exists:users,email',
            'password' => ['required', 'confirmed', RulesPassword::defaults()],
        ]); 
        $otpValid=$this->otp->validate($request->email,$request->otp);
        if(!$otpValid->status){
            return response()->json(['error'=>"Invalid OTP"],401);
        }
         $user=User::where('email',$request->email)->first();
        $user->update(['password'=>Hash::make($request->password)]);
        $user->tokens()->delete();
        //return token for new created user
        return response([
            'message'=>'sucess change the password',
            'status'=>'200'
        ], 500); 
    }
    //
}
