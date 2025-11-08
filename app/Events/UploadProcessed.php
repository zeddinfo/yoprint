<?php

namespace App\Events;

use App\Models\Upload;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UploadProcessed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $upload;

    public function __construct(Upload $upload)
    {
        $this->upload = $upload;
    }

    public function broadcastOn()
    {
        return new Channel('uploads');
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->upload->id,
            'status' => $this->upload->status,
        ];
    }
}