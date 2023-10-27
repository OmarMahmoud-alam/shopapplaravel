<?php

namespace App\Http\Controllers\Api\Chat;

use App\Models\chat;
use App\Models\User;
use App\Models\messagechat;
use Illuminate\Http\Request;
use App\Events\NewMessageSent;
use App\Http\Controllers\Controller;
use Log;
use Symfony\Component\HttpFoundation\JsonResponse;

class MesagechatController extends Controller
{
    public function index(request $request): JsonResponse
    {
        $res=$request->validate([
            'otheruser' => 'required|exists:users,id',
        ]);
        $pageSize = $request->page_size ?? 15;
        $currentPage = $request->page??1;

        if((!is_numeric($pageSize)) || $pageSize<2||$pageSize>200 )
        {
            return response()->json(['error'=>'page_size must be number between 1 and 200'], 200);
        }
        if(!is_numeric($currentPage)){
             return response()->json(['error'=>'currentPage must be number '], 200);

        }

       $user1 = auth()->user()->id;
        $user2 = $request->otheruser;
        
        if( $user1 > $user2){
            $user1_id=$user1;
            $user2_id =$user2;
        }
        else{
            $user2_id=$user1;
            $user1_id =$user2;

        }



        $chatId = $user1.$user2;
       // return response()->json($chatId, 200); 

        $messages = messagechat::where('chat_id', $chatId)->with('user')
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
           'data'=>$messages->getCollection(),
           'total pages'=>$messages->lastPage()
        ], 200);  
  }


    public function store(request $request) : JsonResponse
    {
        $request->validate([
            'reciever_id' => 'required|exists:users,id',
            'message'=>'required'
        ]);
        $sender = auth()->user();
        Log::info("111");

        $data['sender_id'] = auth()->user()->id;
        $data['reciever_id'] = $request->reciever_id;

        $data['message'] = $request->message;
        if( $data['sender_id']>$data['reciever_id']){
            $user1=$data['sender_id'];
            $user2 =$data['reciever_id'];
            $data['chat_id']= $data['sender_id'].$data['reciever_id'];
        }
        else{
            $user2=$data['sender_id'];
            $user1 =$data['reciever_id'];
            $data['chat_id']=$data['reciever_id']. $data['sender_id'];

        }
        $chat=chat::where('user1_id',$user1)->where('user2_id',$user2)->first();
        if(!$chat){
        $chatMessage = chat::create([
            'user1_id'=>$user1,
            'user2_id'=>$user2
        ]);

         }
        
        $chatMessage = messagechat::create($data);
        $chatMessage->load('user');
        Log::info($chatMessage);
//        broadcast(new MessageSentEvent($chatMessage))->toOthers();
        /// TODO send broadcast event to pusher and send notification to onesignal services
        $this->sendNotificationToOther($chatMessage,);
        Log::info(22);

        Log::info($chatMessage);

      return response()->json([
        
        'success' => true,
        'message' => $chatMessage,
    ], 200);
    }

    /**
     * Send notification to other users
     *
     */
    private function sendNotificationToOther(messagechat $chatMessage,) : void {

        // TODO move this event broadcast to observer
        Log::info('chat.' . $chatMessage->chatid);

        Log::info($chatMessage->chat_id);
        broadcast(new NewMessageSent($chatMessage))->toOthers();

        $user = auth()->user();
        $userId = $user->id;

      /*  $chat = Chat::where('id',$chatMessage->chat_id)
            ->with(['participants'=>function($query) use ($userId){
                $query->where('user_id','!=',$userId);
            }])
            ->first();*/
            $receiver=User::where('id',$chatMessage->reciever_id)->first();
            Log::info("hahah".$receiver);
         /*   $receiver->sendNewMessageNotification([
                'messageData'=>[
                    'senderName'=>$receiver->name,
                    'message'=>$chatMessage->message,
                    'chatId'=>$chatMessage->chat_id
                ]
            ]);*/
    

    }


}
