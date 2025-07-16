<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Objective;
use App\Models\Task;
use App\Models\OkrCycle;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $companyId = $user->company_id;

        $objectivesQuery = Objective::with(['user', 'keyResults.assignee'])
            ->where('company_id', $companyId);

        if (!$user->hasRole('super-admin')) {
            $objectivesQuery->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('keyResults', function ($q) use ($user) {
                        $q->where('assignee_id', $user->id);
                    });
            });
        }

        $objectives = $objectivesQuery->latest()->get();

        $tasks = Task::with(['creator', 'assignee', 'keyResult.objective'])
            ->whereHas('keyResult.objective', function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->latest()
            ->get();

        $users = User::where('company_id', $companyId)->get();

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

        // Get current or active OKR cycle for automatic assignment
        $currentCycle = OkrCycle::getCurrent();
        if (!$currentCycle) {
            $currentCycle = OkrCycle::active()->first();
        }

        // If still no cycle, create one for the current quarter
        if (!$currentCycle) {
            $now = Carbon::now();
            $currentCycle = OkrCycle::create([
                'name' => "Q{$now->quarter}-{$now->year}",
                'year' => $now->year,
                'quarter' => $now->quarter,
                'start_date' => $now->startOfQuarter(),
                'end_date' => $now->endOfQuarter(),
                'status' => 'active',
                'description' => "Auto-created Q{$now->quarter} {$now->year} OKR Cycle"
            ]);
        }

        $objective = Objective::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'user_id' => $validated['user_id'],
            'creator_id' => auth()->id(),
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => 'not_started',
            'time_period' => 'quarterly',
            'progress' => 0,
            'company_id' => auth()->user()->company_id,
            // Automatically assign to current cycle
            'cycle_id' => $currentCycle->name,
            'cycle_year' => $currentCycle->year,
            'cycle_quarter' => $currentCycle->quarter,
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
