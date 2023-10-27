<?php

namespace App\Http\Controllers\Api;
use App\Model;

use App\Models\book;
use App\Models\User;
use App\Models\rating;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function userprofile(Request $request)
    {
        $user=auth('sanctum')->user();//->with('photos');
        $user['image']=$user->photos()->first(['src']);
      //  $data=['data'=>$user, 'status'=>200];
       // return response()->json($data, 200);
       $avgrate = rating::where('seller_id',$request->user_id)->avg('rating');
       //$book['photo']=url(Storage::url($filename));
      
        $user['avergerate']=$avgrate;
       // $user['image']=url(Storage::url('imagesfp'.$user->photos->src));
        $user['image']=$user->getprofileimage();
        return response()->json($user);

    }
    public function otheruserprofile(Request $request)
    {
        $validator =Validator::make( $request->all(),[
            'user_id' => 'required|exists:users,id',
        ]);
        $vistitor=auth('sanctum')->user();//->with('photos');

        if($validator->fails()){
            return Response()->Json(['error'=>$validator->messages(),'status'=>422], 406);
           // return Response()->Json(['error'=>$validator->errors(),'status'=>422], 406);
        }
        $user=User::where('id',$request->user_id)->with('books') ->first();
        
        foreach ($user['books'] as $key => $onebook) {
            $onebook['image']=$onebook->getfirsturl();
           }
        
        

        $rate = rating::where('user_id',$vistitor->id)->where('seller_id',$request->user_id)->first("rating");
        $avgrate = rating::where('seller_id',$request->user_id)->avg('rating');
        $user['numberofrating'] = rating::where('seller_id',$request->user_id)->count();
        $user['booksnumber'] = book::where('user_id',$request->user_id)->count();

       //$book['photo']=url(Storage::url($filename));
       if($rate==null){
        $user['myrate']=null;
       }
       else{
        $user['myrate']=$rate["rating"];

       }

        $user['avergerate']=$avgrate;
        $user['image']=$user->getprofileimage();
            



        return response()->json(['data'=> $user,'status'=>200],200);

    }
    public function updateUser(Request $request){
        $validator =Validator::make( $request->all(),[
            'phone' => 'digits:11',
            'name' => 'string',
            'Darkmode' => 'boolean',
            'state' => 'string',
            'address_id'=>'exists:Addresses,id'
                
         ]);
         Log::info($request);
     
         Log::info(gettype($request->Darkmode));
            if($validator->fails()){
            return Response()->Json(['error'=>$validator->messages(),'status'=>422], 200);
           // return Response()->Json(['error'=>$validator->errors(),'status'=>422], 406);
         }
         $user=auth('sanctum')->user();
         if($request->phone){$user->phone=$request->phone;}
            if($request->name){$user->name=$request->name;}
         if($request->Darkmode!==null){
            Log::info('enter darkmode');
            $user->darkmode=$request->Darkmode;}
         if($request->state){$user->state=$request->state;}
         $user->save();
         return response()->json(['message'=>'update data success','data'=>$user], 200);

    }
    
    public function updateUserimage(Request $request){
        $validator =Validator::make( $request->all(),[
            'src' => 'required|string',
            'type' => 'required|string',
  
                
        ]);
        if($validator->fails()){
            return Response()->Json(['error'=>$validator->messages(),'status'=>422], 406);
           // return Response()->Json(['error'=>$validator->errors(),'status'=>422], 406);
        }
        $user=auth('sanctum')->user();
        
        $user->photos()->create([
            'src'=>'dasdasdasd',
            'type'=>'dadadasdasdasdasa',
            
        ]);
        return response()->json(['message'=>'update image success','data'=>$user->photos()->get()], 200);
       // ['src']
    }



}
