<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Notifications\resetpasswordotp;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password as RulesPassword;

class NewPasswordController extends Controller
{
    private $otp;

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status == Password::RESET_LINK_SENT) {
            return [
                'status' => __($status)
            ];
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', RulesPassword::defaults()],
        ]);
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                $user->tokens()->delete();

                event(new PasswordReset($user));
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return response([
                'message'=> 'Password reset successfully'
            ]);
        }

        return response([
            'message'=> __($status)
        ], 500);

    }
    public function resetpasswordotp(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'otp' => 'required',
            'email' => 'required|email',
            'password' => ['required', RulesPassword::defaults()],
        ]);
    $otpVal=$this->otp->validate($request->email,$request->otp);
    if(!$otpVal->status){

        return response()->json(['error'=> $otpVal], 200);
    
    }
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                $user->tokens()->delete();

                event(new PasswordReset($user));
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return response([
                'message'=> 'Password reset successfully'
            ]);
        }

        return response([
            'message'=> __($status)
        ], 500);

    }
    public function forgetpasswordotp(Request $request){
       try{
        $request->validate([
            'email' => 'required|email|exists:users,email',
            
        ]);
        $input=$request->only('email');
        $user=User::where('email',$input)->first();
        $user->notify(new resetpasswordotp);
        return response(['message'=> 'otp code send success', 'status'=>200]);
    }
    catch(Exception $e){
        return response()->json(['error'=>$e ,'tt'=>55], 200);
    }
    }


}