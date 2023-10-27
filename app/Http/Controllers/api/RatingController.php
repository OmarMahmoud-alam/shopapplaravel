<?php

namespace App\Http\Controllers\Api;

use App\Models\rating;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class RatingController extends Controller
{
    public function store(Request $request){
        $validator=Validator::make( $request->all(),[
         "seller_id"=>"required|exists:App\Models\user,id",
        "rating"=>'required|integer|between:1,5'
        ]
     );
      // $user_id=auth::user();
     $user_id=auth('sanctum')->user()->id;
     $seller_id = $request->seller_id;
        $rating = $request->rating;

     if($validator->fails()){
        return Response()->Json(['error'=>$validator->messages(),'status'=>422], 200);
       // return Response()->Json(['error'=>$validator->errors(),'status'=>422], 406);
     }
     if($user_id==$request->seller_id){
        return Response()->Json(['error'=>'can\'t rating yourself norm','status'=>422], 200);
       // return Response()->Json(['error'=>$validator->errors(),'status'=>422], 406);
      }
      else{

        $addedbefore = rating::where('user_id',$user_id)->where('seller_id',$request->seller_id)->get();
        if(count($addedbefore)){ 
        $addedbefore = rating::where('user_id',$user_id)->where('seller_id',$request->seller_id) ->update(['rating' => $rating,]);

            return  response()->json([
                'status'=>200,
                'message'=>'the rating update success',
            ], 200);
        }
        $rating=rating::create([
            'user_id'=>$user_id,
            'seller_id'=>$seller_id,
            'rating'=>$rating,

        ]);

    
        if ($rating) {
            return  response()->json([
                'status'=>200,
                'message'=>'Success rating done',
                'rating'=>$rating
            ], 200);
        }else{
            return  response()->json([
                'status'=>404,
                'message'=>'Their is an error happened',
            ], 404);
           }
     }

 }

 public function show($id){
    $rating = rating::where('seller_id',$id)->avg('rating');
        if ($rating){
            return response()->json([ 
            'data'=>$rating,
               'status'=>200
            ], 200);
          }

          else{
          return   response()->json([ 
            'message'=>'haven\'t any favourite yet ',
               'status'=>403
            ]);
    }
}

//
//
}
