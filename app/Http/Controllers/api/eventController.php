<?php

namespace App\Http\Controllers\Api;

use App\Models\event;
use App\Models\eventComment;
use App\Models\eventInterest;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class eventController extends Controller
{
    public function store(Request $request){
        $validator=Validator::make( $request->all(),[
          
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
            return  response()->json([
                'status'=>200,
                'message'=>'event created right',
                'data'=>$event
            ], 200);
        }else{
            return  response()->json([
                'status'=>404,
                'message'=>'their is an error happened',
            ], 404);
        }
    }

    }

    public function show(){
        $events = event :: all()->orderBy('created_at', 'desc') ;
        if($events){
            return response()->json(["status"=>true,"message"=>"All Events","data"=>$events]);
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
        $pageSize = $request->page_size ?? 15;
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
            ->simplePaginate(
                $pageSize,
                ['*'],
                'page',
                $currentPage
            );

        return response()->json([
         'message '=>'succes',
           'data'=>$comment
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
    if($events){
        return response()->json([
            'message '=>'succes',
              'data'=>$events,
           ], 200); 
    }

    }
}
