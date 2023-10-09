<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ResetPasswordOTp;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\api\booksController;
use App\Http\Controllers\Api\eventController;
use App\Http\Controllers\Api\PhotoController;
use App\Http\Controllers\Api\RatingController;
use App\Http\Controllers\Api\addresseController;
use App\Http\Controllers\api\AuthoUserController;
use App\Http\Controllers\Api\FavouriteController;
use App\Http\Controllers\Api\NewPasswordController;
use App\Http\Controllers\Api\chat\mesagechatController;
use App\Http\Controllers\Api\EmailVerificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthoUserController::class, 'login']);
    Route::post('register', [AuthoUserController::class, 'register']);

    Route::group(['middleware' => 'auth:sanctum'], function() {
      Route::get('logoutalldevices', [AuthoUserController::class, 'logout']);
      Route::get('logout', [AuthoUserController::class, 'logoutonly']);
      //      
    });
});
Route::post('book',[booksController::class,'store'])->middleware(['auth:sanctum','verified']);
Route::get('book',[booksController::class,'index'])->middleware(['auth:sanctum','verified']);
Route::get('category',[booksController::class,'get_category']);
Route::delete('book/{id}/delete',[booksController::class,'destroy'])->middleware('auth:sanctum');
Route::get('book/{id}',[booksController::class,'show'])->middleware('auth:sanctum');
Route::get('usersbook/{id}',[booksController::class,'userproduct'])->middleware('auth:sanctum');
Route::get('books/filter',[booksController::class,'filterbook'])->middleware('auth:sanctum');
Route::post('favourite',[FavouriteController::class,'store'])->middleware('auth:sanctum');
Route::get('favourite',[FavouriteController::class,'show'])->middleware('auth:sanctum');
Route::delete('favourite/delete',[FavouriteController::class,'destroy'])->middleware('auth:sanctum');
Route::post('rate',[RatingController::class,'store'])->middleware('auth:sanctum');
Route::get('rate/{id}',[RatingController::class,'show'])->middleware('auth:sanctum');
//////
Route::post('email/verification-notification', [EmailVerificationController::class, 'sendVerificationEmail'])->middleware('auth:sanctum');
Route::get('verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])->name('verification.verify')->middleware('auth:sanctum');

Route::post('forgot-password', [NewPasswordController::class, 'forgotPassword']);
Route::post('reset-password', [NewPasswordController::class, 'reset']);
Route::get('verify/otp/resend', [AuthoUserController::class, 'resendEmailVerificationOtp'])->middleware('auth:sanctum');
Route::post('verifiedby/otp', [EmailVerificationController::class, 'email_verificationOtp'])->middleware('auth:sanctum');
Route::post('forgot-password/otp', [NewPasswordController::class, 'forgetpasswordotp']);
Route::post('uploadimage', [PhotoController::class, 'storeimage']);

Route::group(['middleware' => 'auth:sanctum'], function() {
  Route::get('getmessage', [mesagechatController::class, 'index']);
  Route::post('message', [mesagechatController::class, 'store']);

});
Route::group(['middleware' => 'auth:sanctum'], function() {
  Route::get('userprofile', [UserController::class, 'userprofile']);
  Route::get('otheruserprofile', [UserController::class, 'otheruserprofile']);
  Route::post('addresse', [addresseController::class, 'store']);
  Route::post('updateuser', [UserController::class, 'updateUser']);
  Route::post('updateUserimage', [UserController::class, 'updateUserimage']);
  Route::get('addresse', [addresseController::class, 'show']);
});

Route::group(['prefix' => 'event'], function () {
  Route::group(['middleware' => 'auth:sanctum'], function() {
    Route::get('', [eventController::class, 'showevent']);
    Route::get('/all', [eventController::class, 'show']);
    Route::post('', [eventController::class, 'store']);
    Route::post('/comment', [eventController::class, 'createcomment']);
    Route::post('/interest', [eventController::class, 'createeventinterst']);
    Route::get('comment', [eventController::class, 'showcomment']);
    Route::get('addresse', [AddresseController::class, 'show']);
  });
});



