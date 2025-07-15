<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\KeyResult;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = auth()->user()->assignedTasks()
            ->with(['keyResult.objective', 'assignee']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $tasks = $query->latest()->paginate(10);
        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        $keyResults = KeyResult::whereHas('objective', function ($query) {
            $query->where('user_id', auth()->id());
        })->get();
        
        $users = User::all();
        
        return view('tasks.create', compact('keyResults', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'nullable|date',
            'key_result_id' => 'required|exists:key_results,id',
            'assignee_id' => 'required|exists:users,id',
        ]);

        // Get the objective_id from the selected key result
        $keyResult = KeyResult::findOrFail($validated['key_result_id']);

        $task = Task::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'due_date' => $validated['due_date'],
            'key_result_id' => $validated['key_result_id'],
            'objective_id' => $keyResult->objective_id,
            'assignee_id' => $validated['assignee_id'],
            'creator_id' => auth()->id(),
            'status' => 'pending_acceptance', // Changed from 'pending' to 'pending_acceptance'
        ]);

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Task created successfully.');
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);
        $task->load(['keyResult.objective', 'assignee', 'creator', 'activities.causer']);
        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $this->authorize('update', $task);
        
        $keyResults = KeyResult::whereHas('objective', function ($query) {
            $query->where('user_id', auth()->id());
        })->get();
        
        $users = User::all();
        
        return view('tasks.edit', compact('task', 'keyResults', 'users'));
    }

    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'nullable|date',
            'key_result_id' => 'required|exists:key_results,id',
            'assignee_id' => 'required|exists:users,id',
            'status' => 'required|in:assigned,pending_acceptance,accepted,rejected,in_progress,completed,blocked', // Updated status options
        ]);

        // Get the objective_id from the selected key result
        $keyResult = KeyResult::findOrFail($validated['key_result_id']);
        $validated['objective_id'] = $keyResult->objective_id;

        $task->update($validated);

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        
        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Task deleted successfully.');
    }

    public function complete(Task $task)
    {
        $this->authorize('update', $task);
        
        $task->update(['status' => 'completed']);

        return back()->with('success', 'Task marked as completed.');
    }
}
