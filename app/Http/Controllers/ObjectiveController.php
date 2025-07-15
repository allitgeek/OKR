<?php

namespace App\Http\Controllers;

use App\Models\Objective;
use App\Models\KeyResult;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ObjectiveController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->authorizeResource(Objective::class, 'objective');
    }

    public function index()
    {
        $objectives = auth()->user()->objectives()
            ->with('keyResults')
            ->latest()
            ->paginate(10); // Paginate with 10 items per page
        return view('objectives.index', compact('objectives'));
    }

    public function create()
    {
        return view('objectives.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'time_period' => 'required|in:monthly,quarterly,yearly',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'not_started';
        $validated['progress'] = 0;

        $objective = Objective::create($validated);

        return redirect()->route('objectives.show', $objective)
            ->with('success', 'Objective created successfully.');
    }

    public function show(Objective $objective)
    {
        return view('objectives.show', compact('objective'));
    }

    public function edit(Objective $objective)
    {
        $users = User::all();
        return view('objectives.edit', compact('objective', 'users'));
    }

    public function update(Request $request, Objective $objective)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'time_period' => 'required|in:monthly,quarterly,yearly',
        ]);

        $objective->update($validated);

        return redirect()->route('objectives.show', $objective)
            ->with('success', 'Objective updated successfully.');
    }

    public function destroy(Objective $objective)
    {
        $objective->delete();

        return redirect()->route('objectives.index')
            ->with('success', 'Objective deleted successfully.');
    }

    public function addKeyResult(Request $request, Objective $objective)
    {
        $this->authorize('update', $objective);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'target_value' => 'required|numeric',
            'current_value' => 'required|numeric',
            'owner_id' => 'required|exists:users,id',
        ]);

        $keyResult = $objective->keyResults()->create([
            'title' => $validated['title'],
            'target_value' => $validated['target_value'],
            'current_value' => $validated['current_value'],
            'owner_id' => $validated['owner_id'],
        ]);

        return redirect()->route('objectives.show', $objective)
            ->with('success', 'Key Result added successfully.');
    }
}
