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
        $user = auth()->user();
        $objectives = Objective::where('company_id', $user->company_id)
            ->with('keyResults')
            ->latest()
            ->paginate(10);
        return view('objectives.index', compact('objectives'));
    }

    public function create()
    {
        return view('objectives.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $objective = Objective::create([
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => $request->user_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'company_id' => auth()->user()->company_id,
        ]);

        return redirect()->route('objectives.index')->with('success', 'Objective created successfully.');
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
