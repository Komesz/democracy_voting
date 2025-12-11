<?php

namespace App\Observers;

use App\Models\Party;
use Illuminate\Support\Str;

class PartyObserver
{
    public function creating(Party $party)
    {
        do {
            $token = Str::random(32);
        } while (Party::query()->where('token', $token)->exists());

        $party->token = $token;
    }
}
