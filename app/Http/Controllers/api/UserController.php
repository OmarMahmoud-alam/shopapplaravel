<?php

namespace App\Http\Controllers\Api;
use App\Model;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validate;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function userprofile(Request $request)
    {
    //    $user=auth('sanctum')->user();
        
      //  $data=['data'=>$user, 'status'=>200];
       // return response()->json($data, 200);
        return response()->json($request->user());

    }
    public function otheruserprofile(Request $request)
    {
        $validator =Validator::make( $request->all(),[
            'user_id' => 'required|exists:users,id',
        ]);
        if($validator->fails()){
            return Response()->Json(['error'=>$validator->messages(),'status'=>422], 406);
           // return Response()->Json(['error'=>$validator->errors(),'status'=>422], 406);
        }
        $user=User::where('id',$request->user_id)->with('books')->first();
        return response()->json(['data'=> $user,'status'=>200],200);

    }
    public function updateUser(Request $request){
        $validator =Validator::make( $request->all(),[
            'phone' => 'digits:10',
            'name' => 'text',
            'Darkmode' => 'boolean',
            'state' => 'text',
            'address_id'=>'exists:Addresses,id'
                
        ]);
        if($validator->fails()){
            return Response()->Json(['error'=>$validator->messages(),'status'=>422], 406);
           // return Response()->Json(['error'=>$validator->errors(),'status'=>422], 406);
        }
        $user=auth('sanctum')->user();
        if($request->phone){$user->phone=$request->phone;}
        if($request->name){$user->name=$request->name;}
        if($request->Darkmode){$user->Darkmode=$request->Darkmode;}
        if($request->state){$user->state=$request->state;}
        $user->save();
        return response()->json(['message'=>'update data success','data'=>$user], 200);

    }
    




}
