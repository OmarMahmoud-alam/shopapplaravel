<?php

namespace App\Http\Controllers\Api;

use auth;
use sanctum;
use App\Models\book;
use App\Models\favourite;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class FavouriteController extends Controller
{
    public function show(){
        $user_id=auth('sanctum')->user()->id;
        $favoriteBooks = book::whereHas('favourites', function ($query) use ($user_id) {
            $query->where('user_id', $user_id);
            })->withCount('favourites')->get();
            if ($favoriteBooks){
                return response()->json([ 
                'data'=>$favoriteBooks,
                   'status'=>200
                ], 200);
              }
       // $book = favourite::where('user_id',$user_id)->books()->get();
      //  $book=$book ->books()->get();
      /*  if ($book){
          return response()->json([ 
          'data'=>$book,
             'status'=>200
          ], 200);
        }*/
        else{
            return   response()->json([ 
                'message'=>'haven\'t any favourite yet ',
                   'status'=>403
                ]);
        }
    }

  public function store(Request $request){
            $validator=Validator::make( $request->all(),[
         "book_id"=>"required|exists:App\Models\book,id",

            ]
        );
       // $user_id=auth::user();
        $user_id=auth('sanctum')->user()->id;

        if($validator->fails()){
            return Response()->Json(['error'=>$validator->messages(),'status'=>422], 406);
           // return Response()->Json(['error'=>$validator->errors(),'status'=>422], 406);
        }
        
        else{
            $addedbefore = favourite::where('user_id',$user_id,)->where('book_id',$request->book_id)->get();
            if(count($addedbefore)){
                return  response()->json([
                    'status'=>400,
                //    '$addedbefore'=>$addedbefore,
                    'message'=>'the book is added to favourite before',
                ], 400);
            }
            $book=favourite::create([
                'user_id'=>$user_id,
                'book_id'=>$request->book_id,

            ]);
        
            if ($book) {
                return  response()->json([
                    'status'=>200,
                    'message'=>'book added to favourite right',
                    'book'=>$book
                ], 200);
            }else{
                return  response()->json([
                    'status'=>404,
                    'message'=>'their is an error happened',
                ], 404);
               }
         }
    
     }
    
 //
 public function destroy(Request $request){
    $validator=Validator::make( $request->all(),[
        "book_id"=>"required|exists:App\Models\book,id",
           ]
       );
      // $user_id=auth::user();
       $user_id=auth('sanctum')->user()->id;
       if($validator->fails()){
           return Response()->Json(['error'=>$validator->messages(),'status'=>422], 406);
          // return Response()->Json(['error'=>$validator->errors(),'status'=>422], 406);
       }

    $book =  favourite::where('user_id',$user_id,)->where('book_id',$request->book_id)->get();
    
    if(count($book)){
        if($book[0]->user_id != $user_id ){
        
            return  response()->json(['message' => 'This user doesn\'t have the permison to do that  ','status'=>'403'],403 );
        }

       
       $books=  favourite::where('user_id',$user_id,)->where('book_id',$request->book_id) ->delete();
        return response()->json([
            'status'=>200 ,
         //   'book'=>$book,
            'message'=>'book deleted succesfully ',
        ]
        , 200);
    }
    else{
        return   response()->json([ 'status'=>404
        ,'message'=>'No book found'],404);
    }
}
//
    }
