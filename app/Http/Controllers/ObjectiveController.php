<?php

namespace App\Http\Controllers;

use App\Models\Objective;
use App\Models\KeyResult;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Cache;

class ObjectiveController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->authorizeResource(Objective::class, 'objective');
    }

    public function index()
    {
        $page = request()->get('page', 1);
        $userId = auth()->id();
        
        $objectives = Cache::remember("user.{$userId}.objectives.page.{$page}", 300, function () {
            return auth()->user()->objectives()
                ->select('id', 'title', 'description', 'status', 'progress', 'start_date', 'end_date', 'time_period')
                ->with(['keyResults:id,objective_id,title,current_value,target_value'])
                ->latest()
                ->paginate(10);
        });
        
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
        $objective = Cache::remember("objective.{$objective->id}", 300, function () use ($objective) {
            return $objective->load([
                'keyResults:id,objective_id,title,current_value,target_value,owner_id',
                'keyResults.owner:id,name',
                'tasks:id,objective_id,title,status'
            ]);
        });
        
        return view('objectives.show', compact('objective'));
    }

    public function edit(Objective $objective)
    {
        $users = Cache::remember('users.list', 3600, function () {
            return User::select('id', 'name')->get();
        });
        
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
        
        // Clear related caches
        Cache::forget("objective.{$objective->id}");
        Cache::forget("user." . auth()->id() . ".objectives.page.1");

        return redirect()->route('objectives.show', $objective)
            ->with('success', 'Objective updated successfully.');
    }

    public function destroy(Objective $objective)
    {
        $objective->delete();
        
        // Clear related caches
        Cache::forget("objective.{$objective->id}");
        Cache::forget("user." . auth()->id() . ".objectives.page.1");

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

        \DB::transaction(function () use ($validated, $objective) {
            $keyResult = $objective->keyResults()->create([
                'title' => $validated['title'],
                'target_value' => $validated['target_value'],
                'current_value' => $validated['current_value'],
                'owner_id' => $validated['owner_id'],
            ]);
            
            // Update objective progress in the same transaction
            $objective->updateProgress();
        });
        
        // Clear related caches
        Cache::forget("objective.{$objective->id}");
        Cache::forget("user." . auth()->id() . ".objectives.page.1");

        return redirect()->route('objectives.show', $objective)
            ->with('success', 'Key Result added successfully.');
    }
}
