<?php

namespace App\Events;

use App\Models\Poll;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\BroadcastEvent;

class VoteReceived implements ShouldBroadcastNow
{
    use SerializesModels, InteractsWithSockets;

    public $poll;
    public $choice;
    public $mandates;

    public function __construct(Poll $poll, $choice, $mandates)
    {
        $this->poll = $poll;
        $this->choice = $choice;
        $this->mandates = $mandates;
    }

    // Broadcasting to a specific channel
    public function broadcastOn()
    {
        return new Channel('poll.' . $this->poll->id); // Channel name is poll.{id}
    }

    // Optional: Broadcast additional data with the event
    public function broadcastWith()
    {
        return [
            'poll' => $this->poll,
            'choice' => $this->choice,
            'mandates' => $this->mandates
        ];
    }
}
