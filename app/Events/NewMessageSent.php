<?php

namespace App\Events;

use App\Models\messagechat;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Log;

class NewMessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(private messagechat $message)
    {
        //

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        Log::info(22222 . 'chat.' . $this->message->chat_id);
        return new PrivateChannel('chat.' . $this->message->chat_id) ;
    }
    public function  broadcastAs(){
        return 'message.sent';
    }
    public function broadcastWith(): array
    {
        return [
            'chat_id' => $this->message->chat_id,
            'message' => $this->message->toArray(),
        ];
    }
}
