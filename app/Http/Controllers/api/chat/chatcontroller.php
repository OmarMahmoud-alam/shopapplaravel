<?php

namespace App\Http\Controllers\Api\Chat;


use App\Http\Controllers\Controller;
use App\Models\chat;
use App\Models\messagechat;

class Chatcontroller extends Controller
{

    //select('user2_id')->
    public function index(){
        $user=auth('sanctum')->user();
        $chat['1']=chat::select('id','user2_id')->where('user1_id',$user->id)->with('users2')->get();
       $chat['2']=chat::select('id','user1_id')->where('user2_id',$user->id)->with('users1')->get();
       
       foreach ($chat['1'] as $key => $oneuser) {
        $oneuser['lastmessage']= messagechat::where('chat_id',$oneuser->id)->latest('created_at')->simplePaginate(
            1,
            ['*'],
            'page',
            1
        )->getCollection();
        $oneuser["users2"]['image']=$oneuser["users2"]->photos()->first(['src']);;
       }
       foreach ($chat['2'] as $key => $oneuser) {
        $oneuser['lastmessage']= messagechat::where('chat_id',$oneuser->id)->latest('created_at')->simplePaginate(
            1,
            ['*'],
            'page',
            1
        )->getCollection();
        $oneuser["users1"]['image']=$oneuser["users1"]->photos()->first(['src']);;
       }
        return response()->json([
              'data'=>$chat,
              'status'=>'200'
        ], 200);


    }
/*
    public function index(request $request): JsonResponse
    {
        $data = $request->validated();
        $chatId = $data['chat_id'];
        $currentPage = $data['page'];
        $pageSize = $data['page_size'] ?? 15;

        $messages = ChatMessage::where('chat_id', $chatId)
            ->with('user')
            ->latest('created_at')
            ->simplePaginate(
                $pageSize,
                ['*'],
                'page',
                $currentPage
            );

        return $this->success($messages->getCollection());
    }


    public function store(request $request) : JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = auth()->user()->id;

        $chatMessage = messagechat::create($data);
        $chatMessage->load('user');

        /// TODO send broadcast event to pusher and send notification to onesignal services
        $this->sendNotificationToOther($chatMessage);

        return $this->success($chatMessage,'Message has been sent successfully.');
    }


    private function sendNotificationToOther(ChatMessage $chatMessage) : void {

        // TODO move this event broadcast to observer
        broadcast(new NewMessageSent($chatMessage))->toOthers();

        $user = auth()->user();
        $userId = $user->id;

        $chat = Chat::where('id',$chatMessage->chat_id)
            ->with(['participants'=>function($query) use ($userId){
                $query->where('user_id','!=',$userId);
            }])
            ->first();
        if(count($chat->participants) > 0){
            $otherUserId = $chat->participants[0]->user_id;

            $otherUser = User::where('id',$otherUserId)->first();
            $otherUser->sendNewMessageNotification([
                'messageData'=>[
                    'senderName'=>$user->username,
                    'message'=>$chatMessage->message,
                    'chatId'=>$chatMessage->chat_id
                ]
            ]);

        }

    }

*/
}



