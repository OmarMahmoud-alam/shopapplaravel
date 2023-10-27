<?php

namespace App\Http\Controllers\Api;

use App\Models\book;
use App\Models\event;
use Illuminate\Http\Request;
use App\Traits\ImageProcessing;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PhotoController extends Controller
{
    use ImageProcessing;
    public function storeimage(request $request)

    {

        $validator =Validator::make( $request->all(),[           
            'type' => 'string',
            'loadtype'=>'required',Rule::in(['profileimage', 'event','book']),
            'book_id'=>'exists:books,id',
            'event_id'=>'exists:events,id',
            'image' => 'required|array',
            'image.*' => 'image|mimes:jpg,jpeg,png,gif,svg,tiff,webp '
        ]);
//if ($validator->fails())  return response()->json($validator->errors());
        
        if($validator->fails()){
            return Response()->Json(['error'=>$validator->messages(),'status'=>422], 406);
           // return Response()->Json(['error'=>$validator->errors(),'status'=>422], 406);
        }
        $user=auth('sanctum')->user();
        info('1');
        $loadtype=$request->loadtype;
        $files = $request->file('image');
        //info('122'.$files);
       // info('122'.gettype($files));
       // $fileName = $request->images[0]->getClientOriginalName();
      //  $print=$request->file('image');

     //if($request->hasFile('image')){
        //$dataX =  $this->saveImageAndThumbnail($request->file('image'),true);
           // $data['image']  =  $dataX['image'];
           // $data['thumbnailsm']  =  $dataX['thumbnailsm'];
        //    $data['thumbnailmd']  =  $dataX['thumbnailmd'];
          //  $data['thumbnailxl']  =  $dataX['thumbnailxl'];
      //  }
      //  else{
      //      return response()->json(['error'=>'no image found'], 200);
     //   }
        //$path=$data['thumbnailmd'];
        $event_id=$request->event_id;
        $book_id=$request->book_id;
        //Log::info('start');
       // $file = Request::file('request')[0]['image'];
       // Log::info($file);
       info('2');

        if($loadtype=='profileimage'){
            $path =  $this->saveImageAndThumbnail($files[0],false);
           // $path =  $this->saveImageAndThumbnail($request->file('image'),false);

            $result=$this->profileImage($user,$path );
            return response()->json($result, 200);
            }
         else if($loadtype=='event'){
            $event=event::where('id',$event_id)->first();
            Log::info('after get event');
            
            if (!$event) {
                return['message'=>'error happen in upload event'];
            }
            if($user->id!=$event->user_id){
                return ['mesage'=>"you are not authorized to access this"];
            }
            
                foreach ($files as $key => $file) {
                $result['mesage']='sucess' ;

                    $path =  $this->saveImageAndThumbnail($file,false);
                    $result= $this->eventImages($user,$path,$event_id);
                }
             // $path =  $this->saveImageAndThumbnail($request->file('image'),false);

           // $result= $this->eventImages($user,$path,$event_id);
            return response()->json($result, 200);
            }
        else if($loadtype=='book'){
        info('3');

            if(!$book_id){

                return   response()->json(["message"=>"please provide book id"],501);
            }

            foreach ($files as $key => $file) {
                $path =  $this->saveImageAndThumbnail($file,false);

                $result= $this->bookImages($user,$path,$book_id);
            }
           // $path =  $this->saveImageAndThumbnail($request->file('image'),false);
            //    $result=$this->bookImages($user,$path,$book_id);
                return response()->json($result, 200);
          }
          else{
            return response()->json(['message'=>'their is any error happend'], 200);
          }

    }
    public function profileImage($user,$path){
        if($user->photos){
            //delete old photo from storage and database
        $this->deleteImage($user->photos->src);
        Log::info(5555555555);

        Log::info($user->photos->src);
        $user->photos->src= $path ;
        $user->photos()->update(['src'=>$path]) ;
      //  $user->photo->save();
        Log::info(3333333333);

        Log::info($user->photos->src);
        }
       else {
        Log::info("44");
        Log::info($path);
        $user->photos()->create([
            'src'=>$path,
            'type'=>'photo',
            
        ]);
    }
        return $user->photos()->get();
  
    }
    public function eventImages($user,$path,$event_id){
        $event=event::where('id',$event_id)->first();
        Log::info('after get event');
        
        if (!$event) {
            return['message'=>'error happen in uploafd event'];
        }
        if($user->id!=$event->user_id){
            return ['mesage'=>"you are not authorized to access this"];
        }

      
         $event->photos()->create([
            'src'=>$path,
            'type'=>'photo',
            
        ]);
        Log::info('save done');

        return ['message'=>'update image success','data'=>$event->photos()->get()];
    }



    public function bookImages($user,$path,$book_id){
        $book=book::where('id',$book_id)->first();

        if($user->id!=$book->user_id){
            return ['mesage'=>"you are not authorized to access this"];
        }
         $book->photos()->create([
            'src'=>$path,
            'type'=>'photo',
            
        ]);

        return ['message'=>'update image success','data'=>$book->photos()->get()];   
     }
}
