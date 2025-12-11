<?php

namespace App\Http\Controllers;

use App\Models\Party;
use Illuminate\Http\Request;

class PartyController extends Controller
{
    public function index()
    {
        return response()->json(Party::query()->orderByDesc('created_at')->get());
    }

    // Create a new poll
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mandates' => 'required|integer|min:1',
            'token' => 'required|string',
        ]);

        if($validated['token'] !== config('app.poll_token')) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        $party = Party::create([
            'name' => $validated['name'],
            'mandates' => $validated['mandates'],
        ]);

        return response()->json($party, 201);
    }

    public function show($token)
    {
        $party = Party::query()->where('token', $token)->firstOrFail();
        return response()->json($party);
    }

    public function destroy($id)
    {
        Party::query()->where('id', $id)->delete();

        return response()->json();
    }
}
