<?php

namespace App\Http\Controllers\Api;

use App\Models\Addresse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AddresseController extends Controller
{
    public function store(Request $request){
        $validator=Validator::make( $request->all(),[
          
            "long"=>"required|numeric| max:191",
            "lat"=>"required|numeric| max:191",
        ]
    );
   // $user_id=auth::user();
    $user_id=auth('sanctum')->user()->id;

    if($validator->fails()){
        return Response()->Json(['error'=>$validator->messages(),'status'=>422], 406);
       // return Response()->Json(['error'=>$validator->errors(),'status'=>422], 406);
    }
    else{
        $addresse=Addresse::create([
            'user_id'=>$user_id,
            'long'=>$request->long,
            'lat'=>$request->lat,

        ]);

    
    if ($addresse) {
            return  response()->json([
                'status'=>200,
                'message'=>'Success',
                'data'=>$addresse
            ], 200);
        }else{
            return  response()->json([
                'status'=>404,
                'message'=>'their is an error happened',
            ], 200);
        }
    }

    }
    public function show(){
    
   // $user_id=auth::user();
    $user_id=auth('sanctum')->user()->id;

  
        $addresse=Addresse::where('user_id',$user_id)->get();

    
    if (count($addresse)>0) {
            return  response()->json([
                'status'=>200,
                'message'=>'Addresse created right',
                'data'=>$addresse
            ], 200);
        }else{
            return  response()->json([
                'status'=>404,
                'message'=>'their is an error happened',
            ], 404);
        }
    }

}
