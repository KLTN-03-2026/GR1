<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NhomChatMessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public array $chat
    ) {
    }

    public function broadcastOn(): array
    {
        \Log::info('NhomChatMessageSent broadcastOn called', [
            'channel' => 'nhom-chat.'.$this->chat['id_nhom_du_lich'],
            'chat_id' => $this->chat['id'] ?? $this->chat['id_tin_nhan']
        ]);

        return [
            new PrivateChannel('nhom-chat.'.$this->chat['id_nhom_du_lich']),
        ];
    }

    public function broadcastAs(): string
    {
        return 'nhom-chat.message.sent';
    }

    public function broadcastWith(): array
    {
        $payload = [
            'chat' => $this->chat,
        ];

        \Log::info('NhomChatMessageSent broadcastWith payload', [
            'event_name' => $this->broadcastAs(),
            'payload' => $payload
        ]);

        return $payload;
    }
}
