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

        // Get relevant objectives based on user role and involvement
        $objectivesQuery = $this->getRelevantObjectivesQuery($user, $companyId);
        $objectives = $objectivesQuery->latest()->get();

        // Get relevant tasks based on user role and involvement
        $tasksQuery = $this->getRelevantTasksQuery($user, $companyId);
        $tasks = $tasksQuery->latest()->get();

        // Get relevant users for the current user's context
        $users = $this->getRelevantUsers($user, $companyId);

        // Add dashboard context for the view
        $dashboardContext = $this->getDashboardContext($user, $objectives, $tasks);

        return view('dashboard', compact('objectives', 'tasks', 'users', 'dashboardContext'));
    }

    /**
     * Get objectives relevant to the current user based on their role and involvement
     */
    private function getRelevantObjectivesQuery($user, $companyId)
    {
        $objectivesQuery = Objective::with(['user', 'keyResults.assignee', 'team'])
            ->where('company_id', $companyId);

        if ($user->hasRole('super-admin')) {
            // Super-admins see strategic overview: only company-level and their direct objectives
            $objectivesQuery->where(function ($query) use ($user) {
                $query->where('level', 'company')
                    ->orWhere('user_id', $user->id)
                    ->orWhereHas('keyResults', function ($q) use ($user) {
                        $q->where('assignee_id', $user->id);
                    });
            });
        } elseif ($user->hasRole('manager') || $user->hasRole('admin')) {
            // Managers see: their objectives + their team's objectives + direct reports' objectives
            $objectivesQuery->where(function ($query) use ($user) {
                $query->where('user_id', $user->id) // Own objectives
                    ->orWhere('team_id', $user->team_id) // Team objectives
                    ->orWhereHas('user', function ($q) use ($user) {
                        $q->where('manager_id', $user->id); // Direct reports' objectives
                    })
                    ->orWhereHas('keyResults', function ($q) use ($user) {
                        $q->where('assignee_id', $user->id); // Assigned key results
                    });
            });
        } else {
            // Regular members see: their objectives + objectives they're involved in + team objectives
            $objectivesQuery->where(function ($query) use ($user) {
                $query->where('user_id', $user->id) // Own objectives
                    ->orWhere('team_id', $user->team_id) // Team objectives (if part of a team)
                    ->orWhereHas('keyResults', function ($q) use ($user) {
                        $q->where('assignee_id', $user->id); // Assigned key results
                    })
                    ->orWhereHas('tasks', function ($q) use ($user) {
                        $q->where('assignee_id', $user->id); // Assigned tasks
                    });
            });
        }

        return $objectivesQuery;
    }

    /**
     * Get tasks relevant to the current user based on their role and involvement
     */
    private function getRelevantTasksQuery($user, $companyId)
    {
        $tasksQuery = Task::with(['creator', 'assignee', 'keyResult.objective'])
            ->whereHas('keyResult.objective', function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            });

        if ($user->hasRole('super-admin')) {
            // Super-admins see overview of critical tasks only
            $tasksQuery->where(function ($query) use ($user) {
                $query->where('priority', 'high')
                    ->orWhere('status', 'overdue')
                    ->orWhere('assignee_id', $user->id)
                    ->orWhere('creator_id', $user->id);
            });
        } elseif ($user->hasRole('manager') || $user->hasRole('admin')) {
            // Managers see: their tasks + their team's tasks + direct reports' tasks
            $tasksQuery->where(function ($query) use ($user) {
                $query->where('assignee_id', $user->id) // Assigned to them
                    ->orWhere('creator_id', $user->id) // Created by them
                    ->orWhereHas('assignee', function ($q) use ($user) {
                        $q->where('manager_id', $user->id) // Direct reports' tasks
                            ->orWhere('team_id', $user->team_id); // Team members' tasks
                    });
            });
        } else {
            // Regular members see: their tasks + tasks they created
            $tasksQuery->where(function ($query) use ($user) {
                $query->where('assignee_id', $user->id) // Assigned to them
                    ->orWhere('creator_id', $user->id); // Created by them
            });
        }

        return $tasksQuery;
    }

    /**
     * Get users relevant to the current user's context
     */
    private function getRelevantUsers($user, $companyId)
    {
        if ($user->hasRole('super-admin')) {
            // Super-admins see all company users
            return User::where('company_id', $companyId)->get();
        } elseif ($user->hasRole('manager') || $user->hasRole('admin')) {
            // Managers see their team and direct reports
            return User::where('company_id', $companyId)
                ->where(function ($query) use ($user) {
                    $query->where('team_id', $user->team_id)
                        ->orWhere('manager_id', $user->id)
                        ->orWhere('id', $user->id);
                })
                ->get();
        } else {
            // Regular members see their team members
            return User::where('company_id', $companyId)
                ->where(function ($query) use ($user) {
                    $query->where('team_id', $user->team_id)
                        ->orWhere('id', $user->id);
                })
                ->get();
        }
    }

    /**
     * Get dashboard context information for personalized display
     */
    private function getDashboardContext($user, $objectives, $tasks)
    {
        $context = [
            'user_role' => $user->roles->first()->name ?? 'Member',
            'user_role_slug' => $user->roles->first()->slug ?? 'member',
            'personalization' => [],
        ];

        // Role-specific context
        if ($user->hasRole('super-admin')) {
            $context['personalization'] = [
                'title' => 'Strategic Overview',
                'description' => 'Company-level objectives and critical items requiring attention',
                'focus' => 'strategic'
            ];
        } elseif ($user->hasRole('manager') || $user->hasRole('admin')) {
            $teamSize = $user->team ? $user->team->users()->count() : 0;
            $directReports = $user->directReports()->count();
            
            $context['personalization'] = [
                'title' => 'Team Management Dashboard',
                'description' => "Your objectives, team progress, and {$directReports} direct reports",
                'focus' => 'team',
                'stats' => [
                    'team_size' => $teamSize,
                    'direct_reports' => $directReports,
                ]
            ];
        } else {
            $context['personalization'] = [
                'title' => 'My OKR Dashboard',
                'description' => 'Your objectives, assigned tasks, and team progress',
                'focus' => 'individual'
            ];
        }

        // Add involvement statistics
        $context['involvement'] = [
            'owned_objectives' => $objectives->where('user_id', $user->id)->count(),
            'assigned_key_results' => $objectives->flatMap->keyResults->where('assignee_id', $user->id)->count(),
            'assigned_tasks' => $tasks->where('assignee_id', $user->id)->count(),
            'overdue_tasks' => $tasks->where('assignee_id', $user->id)->where('status', 'overdue')->count(),
        ];

        return $context;
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
