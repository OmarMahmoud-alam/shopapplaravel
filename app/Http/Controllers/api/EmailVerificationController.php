<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Ichtrojan\Otp\Otp;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use App\Http\Requests\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Validator;


class EmailVerificationController extends Controller
{
    private $otp;
    public function __construct() {
        $this->otp =new Otp;
    }
   public function email_verificationOtp(Request    $request){
    $validator=Validator::make( $request->all(),[
        'email'=>['required','email','exists:App\Models\User,email'],
        'otp'=>['required','max:6'],
        ]); 
        
        if ($validator->fails()) {
            return response()->json(['status'=> "403",
            'message'=> "can't verfiy",
            'result'=>0,
                'error'=>$validator->errors(),
        ],403);
        }

    $otpVal=$this->otp->validate($request->email,$request->otp);
    if(!$otpVal->status){

        return response()->json(['error'=> $otpVal], 401);
    
    }
    $user=User::where('email',$request->email)->first();


    if ($user) {

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
    return response()->json(['message'=>'the email is verified'], 200);

        }

    }

    return response()->json(['error'=>'you have an error'], 403);


   }

    public function sendVerificationEmail(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return [
                'message' => 'Already Verified'
            ];
        }

        $request->user()->sendEmailVerificationNotification();

        return ['status' => 'verification-link-sent'];
    }

    public function verify(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return [
                'message' => 'Email already verified'
            ];
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return [
            'message'=>'Email has been verified'
        ];
    }
}