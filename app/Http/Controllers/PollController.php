<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use Illuminate\Http\Request;

class PollController extends Controller
{
    // List all active polls
    public function index()
    {
        return response()->json(Poll::query()->orderByDesc('created_at')->get());
    }

    // Create a new poll
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'choices' => 'required|array|min:2', // At least two choices required
            'token' => 'required|string',
        ]);

        if($validated['token'] !== config('app.poll_token')) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        $poll = Poll::create([
            'title' => $validated['title'],
            'choices' => $validated['choices'],
            'results' => array_fill_keys($validated['choices'], 0), // Initialize results
            'active' => 1
        ]);

        // Broadcast the new poll
        broadcast(new \App\Events\NewPollCreated($poll));

        return response()->json($poll, 201);
    }

    // Get details of a specific poll
    public function show($id)
    {
        $poll = Poll::findOrFail($id);
        return response()->json($poll);
    }

    public function toggleactivation(Request $request, $id)
    {
        $validated = $request->validate([
            'token' => 'required|string',
        ]);

        if($validated['token'] !== config('app.poll_token')) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        $poll = Poll::findOrFail($id);

        $poll->active = $poll->active ? 0 : 1;
        $poll->save();

        broadcast(new \App\Events\PollActiveChanged($poll));

        return response()->json($poll);
    }

    // Submit a vote
    public function vote(Request $request, $id)
    {
        $poll = Poll::findOrFail($id);

        $validated = $request->validate([
            'choice' => 'required|string|in:' . implode(',', $poll->choices),
        ]);

        $choice = $validated['choice'];
        $results = $poll->results;
        $results[$choice]++;
        $poll->results = $results;
        $poll->save();

        // Broadcast the vote to clients
        broadcast(new \App\Events\VoteReceived($poll, $choice));

        return response()->json($poll);
    }
}
