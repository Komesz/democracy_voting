<?php

namespace App\Events;

use App\Models\Poll;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class NewPollCreated implements ShouldBroadcastNow
{
    use SerializesModels, InteractsWithSockets;

    public $poll;

    public function __construct(Poll $poll)
    {
        $this->poll = $poll;
    }

    public function broadcastOn()
    {
        return new Channel('polls');
    }
}
