<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ItineraryReordered implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $id_nhom_du_lich;
    public $id_chuyen_di;
    public $data;

    public function __construct($idNhom, $idChuyenDi, $data = [])
    {
        $this->id_nhom_du_lich = $idNhom;
        $this->id_chuyen_di = $idChuyenDi;
        $this->data = $data;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('nhom-chat.' . $this->id_nhom_du_lich),
        ];
    }

    public function broadcastAs(): string
    {
        return 'itinerary.reordered';
    }

    public function broadcastWith(): array
    {
        return [
            'id_chuyen_di' => $this->id_chuyen_di,
            'data' => $this->data,
        ];
    }
}
