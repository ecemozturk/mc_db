<?php
namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{

    public function index(Request $request)
    {
        $events = Event::all();
        return response()->json(['data' => $events]);
    }

    public function show(Event $event)
    {
        return response()->json(['data' => $event]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required',
            'description' => 'required',
            'start' => 'required|date_format:Y-m-d H:i:s',
            'end' => 'nullable|date_format:Y-m-d H:i:s|after_or_equal:start',
        ]);

        $event = Event::create($validated);

        return response()->json(['data' => $event], 201);
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required',
            'description' => 'required',
            'start' => 'required|date_format:Y-m-d H:i:s',
            'end' => 'required|date_format:Y-m-d H:i:s|after_or_equal:start',
        ]);

        $event->update($validated);

        return response()->json(['data' => $event->refresh()]);
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return response()->json(['message' => 'Event deleted successfully']);
    }
}
