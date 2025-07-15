<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Objective;
use App\Models\Task;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        if (auth()->user()->hasRole('super-admin')) {
            $objectives = Objective::with(['user', 'keyResults.owner'])
                ->latest()
                ->get();

            $tasks = Task::with(['creator', 'assignee', 'keyResult.objective'])
                ->latest()
                ->get();

            $users = User::where('is_active', true)->get();

            return view('dashboard', compact('objectives', 'tasks', 'users'));
        }

        // Get both objectives where user is assigned and where user is creator
        $objectives = Objective::with(['keyResults.owner'])
            ->where('user_id', auth()->id())
            ->orWhere('creator_id', auth()->id())
            ->latest()
            ->get();

        $tasks = auth()->user()->assignedTasks()
            ->with('keyResult.objective')
            ->latest()
            ->get();

        $users = User::where('is_active', true)->get();

        return view('dashboard', compact('objectives', 'tasks', 'users'));
    }

    public function createObjective(Request $request)
    {
        $this->authorize('manage-objectives');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'user_id' => 'required|exists:users,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        $objective = Objective::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'user_id' => $validated['user_id'],
            'creator_id' => auth()->id(),
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => 'not_started',
            'time_period' => 'quarterly',
            'progress' => 0
        ]);

        return back()->with('success', 'Objective created successfully.');
    }

    public function createTask(Request $request)
    {
        $this->authorize('manage-tasks');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'key_result_id' => 'nullable|exists:key_results,id',
            'assignee_id' => 'required|exists:users,id',
            'start_date' => 'required|date|after_or_equal:today',
            'due_date' => 'required|date|after_or_equal:start_date'
        ]);

        $task = Task::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'key_result_id' => $validated['key_result_id'],
            'objective_id' => $validated['key_result_id'] ? \App\Models\KeyResult::find($validated['key_result_id'])->objective_id : null,
            'assignee_id' => $validated['assignee_id'],
            'creator_id' => auth()->id(),
            'start_date' => $validated['start_date'],
            'due_date' => $validated['due_date'],
            'status' => 'assigned'
        ]);

        return back()->with('success', 'Task created successfully.');
    }
}
