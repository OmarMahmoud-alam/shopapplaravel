<?php

namespace App\Http\Controllers\Api;

use DB;
use App\Models\event;
use App\Models\eventComment;
use Illuminate\Http\Request;
use App\Models\eventInterest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Traits\ImageProcessing;

class eventController extends Controller
{    use ImageProcessing;

    public function store(Request $request){
        $validator=Validator::make( $request->all(),[
            'image' => 'required|array',
            'image.*' => 'image|mimes:jpg,jpeg,png,gif,svg,tiff,webp ',
            "place_link"=>"required|string| max:191",
            "discription"=>"required|string| max:400",
            "name"=>"required|string| max:100",
            "startat"=>"required|date",
            "endedat"=>"required|date|after_or_equal:startat",
            "online"=>"boolean| max:191",
 
        ]
    );


   // $user_id=auth::user();
    $user_id=auth('sanctum')->user()->id;



    if($validator->fails()){
        return Response()->Json(['error'=>$validator->messages(),'status'=>422], 406);
       // return Response()->Json(['error'=>$validator->errors(),'status'=>422], 406);
    }
    else{
        $data['user_id']=$user_id;
        $data['place_link']=$request->place_link;
        $data['name']=$request->name??'noname';
        $data['discription']=$request->discription;
        $data['startat']=$request->startat;
        $data['endedat']=$request->endedat;
        $data['online']=$request->online ??false;
        $event=event::create($data);


    
    if ($event) {
        $files = $request->file('image');

       $image= $this->storeimage($event->id,$files );
            return  response()->json([
                'status'=>200,
                'message'=>'event created right',
                'data'=>$event,
                'image'=>$image,
            ], 200);
        }else{
            return  response()->json([
                'status'=>404,
                'message'=>'their is an error happened',
            ], 404);
        }
    }

    }

    public function show(request $request){

        $pageSize = $request->page_size ?? 25;
        $currentPage = $request->page??1;

        if((!is_numeric($pageSize)) || $pageSize<2||$pageSize>200 )
        {
            return response()->json(['error'=>'page_size must be number between 2 and 200'], 200);
        }
        if(!is_numeric($currentPage)){
             return response()->json(['error'=>'currentPage must be number '], 200);

        }
        $events = event ::orderBy('created_at') ->Paginate(//, 'desc'
            $pageSize,
            ['*'],
            'page',
            $currentPage
        ) ;
        foreach ($events as $key => $oneven) {
            $oneven['image']=$oneven->getfirsturl();
        }
        if($events){
            return response()->json(["status"=>true,"message"=>"All Events","data"=>$events,
            'lastpage'=>$events->lastPage(),
        ]);
        }
        else{
            return response()->json(["status"=>false,"message"=>"No Event Found"]);
        }
    }
    public function showcomment(request $request){   
        $validator=Validator::make( $request->all(),[ 
            "event_id"=>"required|exists:events,id",
            "page_size"=>'numeric'
        ]
    );

    if($validator->fails()){
        return Response()->Json(['error'=>$validator->messages(),'status'=>422], 406);
       // return Response()->Json(['error'=>$validator->errors(),'status'=>422], 406);
    }
        $pageSize = $request->page_size ?? 25;
        $currentPage = $request->page??1;

        if((!is_numeric($pageSize)) || $pageSize<2||$pageSize>200 )
        {
            return response()->json(['error'=>'page_size must be number between 1 and 200'], 200);
        }
        if(!is_numeric($currentPage)){
             return response()->json(['error'=>'currentPage must be number '], 200);

        }
        $comment = eventComment::where('event_id', $request->event_id)
            ->orderBy('created_at', 'desc')
           // ->latest('created_at')
            ->Paginate(
                $pageSize,
                ['*'],
                'page',
                $currentPage
            );

        return response()->json([
         'message '=>'succes',
           'data'=>$comment,
           'lastpage'=>$comment->lastPage(),
        ], 200);  
 
    }
    public function createcomment(request $request){   
        $validator=Validator::make( $request->all(),[ 
            "event_id"=>"required|exists:events,id",
            "comment"=>'required|string|max:200'
        ]
    );

    if($validator->fails()){
        return Response()->Json(['error'=>$validator->messages(),'status'=>422], 406);
       // return Response()->Json(['error'=>$validator->errors(),'status'=>422], 406);
    }
    $user_id=auth('sanctum')->user()->id;

       
        $comment = eventComment::create(['user_id'=>$user_id
                        ,"event_id"=>$request->event_id,"comment"=>$request->comment]);
        if($comment){
                    return response()->json([
         'message '=>'succes',
           'data'=>$comment
        ], 200);  
        }
        else{
            return response()->json([
                'message '=>'their an error happened',
               ], 200);  
        }

 
    }
    public function createeventinterst(request $request){   
        $validator=Validator::make( $request->all(),[ 
            "event_id"=>"required|exists:events,id",
            "type"=>Rule::in(['interset', 'comming','delete'])
        ]
         );

         if($validator->fails()){
        return Response()->Json(['error'=>$validator->messages(),'status'=>422], 406);
       // return Response()->Json(['error'=>$validator->errors(),'status'=>422], 406);
     }
         $user_id=auth('sanctum')->user()->id;
         $eventinterstfound=eventInterest::where('user_id',$user_id)->where("event_id",$request->event_id)->first();
        if($eventinterstfound ){
            if($request->type=='delete'){
                $eventinterstfound->delete();
                return response()->json(['message'=>'delete success'], 200);
            }
            $eventinterstfound->type=$request->type;
            $eventinterstfound->save();
            return response()->json(['message'=>' success update '], 200);

        }
        if($request->type=='delete'){
            return response()->json(['message'=>'this user has no record here'], 200);

        }
        $eventinterst = eventInterest::create(['user_id'=>$user_id
                        ,"event_id"=>$request->event_id,"type"=>$request->type]);
        if($eventinterst){
                    return response()->json([
         'message '=>'succes',
           'data'=>$eventinterst
        ], 200);  
        }
        else{
            return response()->json([
                'message '=>'their an error happened',
               ], 200);  
        }

 
    }
    public function showevent(request $request){
        $validator=Validator::make( $request->all(),[ 
            "event_id"=>"required|exists:events,id",
        ]
        );

        if($validator->fails()){
        return Response()->Json(['error'=>$validator->messages(),'status'=>422], 406);
       // return Response()->Json(['error'=>$validator->errors(),'status'=>422], 406);
    }
    $events = event::where("id",$request->event_id)->with('eventcomment' )->first();
    $events['interest']=eventInterest::select('type', DB::raw('count(*) as count'))
    ->groupBy('type')->get();
    $events['image']=$events->getallurl();

    if($events){
        return response()->json([
            'message '=>'succes',
              'data'=>$events,
           ], 200); 
    }

    }


    public function storeimage($event_id,$files )
    {

        $user=auth('sanctum')->user();
        info('1' .$event_id);
       // info('122'.$files);
        info('122'.gettype($files));
  
        $event=event::where('id',$event_id)->first();
        Log::info('after get event');
        
        if (!$event) {
            return['message'=>'error happen in upload event'];
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
}
