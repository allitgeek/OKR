<?php

namespace App\Http\Controllers;

use App\Models\{OkrCheckIn, Objective, KeyResult};
use Illuminate\Http\Request;
use Carbon\Carbon;

class OkrCheckInController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = OkrCheckIn::with(['objective', 'keyResult', 'user'])
            ->where('user_id', auth()->id());

        // Apply filters
        if ($request->filled('type')) {
            $query->where('check_in_type', $request->type);
        }

        if ($request->filled('period')) {
            match($request->period) {
                'week' => $query->where('check_in_date', '>=', Carbon::now()->subWeek()),
                'month' => $query->where('check_in_date', '>=', Carbon::now()->subMonth()),
                'quarter' => $query->where('check_in_date', '>=', Carbon::now()->subQuarter()),
                default => null
            };
        }

        $checkIns = $query->latest('check_in_date')->paginate(15);

        return view('okr-check-ins.index', compact('checkIns'));
    }

    public function create(Request $request)
    {
        $objectiveId = $request->objective_id;
        $keyResultId = $request->key_result_id;

        // Validate that user can check-in on this objective/key result
        if ($objectiveId) {
            $objective = Objective::findOrFail($objectiveId);
            if ($objective->user_id !== auth()->id() && $objective->creator_id !== auth()->id()) {
                abort(403, 'You can only check-in on your own objectives.');
            }
        }

        if ($keyResultId) {
            $keyResult = KeyResult::findOrFail($keyResultId);
            if ($keyResult->owner_id !== auth()->id()) {
                abort(403, 'You can only check-in on your own key results.');
            }
            $objectiveId = $keyResult->objective_id;
        }

        $objectives = Objective::where(function($query) {
            $query->where('user_id', auth()->id())
                  ->orWhere('creator_id', auth()->id());
        })->with('keyResults')->get();

        $keyResults = KeyResult::whereHas('objective', function($query) {
            $query->where('user_id', auth()->id())
                  ->orWhere('creator_id', auth()->id());
        })->with('objective')->get();

        return view('okr-check-ins.create', compact('objectives', 'keyResults', 'objectiveId', 'keyResultId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'objective_id' => 'nullable|exists:objectives,id',
            'key_result_id' => 'nullable|exists:key_results,id',
            'confidence_level' => 'required|numeric|min:0|max:1',
            'progress_notes' => 'required|string',
            'challenges' => 'nullable|string',
            'next_steps' => 'required|string',
            'risk_factors' => 'nullable|array',
            'check_in_type' => 'required|in:weekly,bi_weekly,monthly,quarterly,ad_hoc',
            'check_in_date' => 'required|date'
        ]);

        // Validate ownership
        if ($request->objective_id) {
            $objective = Objective::findOrFail($request->objective_id);
            if ($objective->user_id !== auth()->id() && $objective->creator_id !== auth()->id()) {
                abort(403);
            }
        }

        if ($request->key_result_id) {
            $keyResult = KeyResult::findOrFail($request->key_result_id);
            if ($keyResult->owner_id !== auth()->id()) {
                abort(403);
            }
        }

        // Get previous progress for tracking changes
        $previousProgress = 0;
        $currentProgress = 0;

        if ($request->objective_id) {
            $objective = Objective::find($request->objective_id);
            $previousProgress = $objective->okr_score ?? 0;
            $currentProgress = $objective->calculateOkrScore();
        }

        if ($request->key_result_id) {
            $keyResult = KeyResult::find($request->key_result_id);
            $previousProgress = $keyResult->okr_score ?? 0;
            $currentProgress = $keyResult->calculateOkrScore();
        }

        $checkIn = OkrCheckIn::create([
            'objective_id' => $request->objective_id,
            'key_result_id' => $request->key_result_id,
            'user_id' => auth()->id(),
            'previous_progress' => $previousProgress,
            'current_progress' => $currentProgress,
            'confidence_level' => $request->confidence_level,
            'progress_notes' => $request->progress_notes,
            'challenges' => $request->challenges,
            'next_steps' => $request->next_steps,
            'risk_factors' => $request->risk_factors ?? [],
            'check_in_type' => $request->check_in_type,
            'check_in_date' => $request->check_in_date
        ]);

        // Update the objective/key result with latest check-in info
        if ($request->objective_id) {
            $objective->update([
                'confidence_level' => $request->confidence_level,
                'last_check_in' => now(),
                'last_check_in_notes' => $request->progress_notes
            ]);
        }

        if ($request->key_result_id) {
            $keyResult->update([
                'confidence_level' => $request->confidence_level,
                'last_check_in' => now(),
                'last_check_in_notes' => $request->progress_notes
            ]);
        }

        return redirect()->route('okr-check-ins.show', $checkIn)
            ->with('success', 'Check-in recorded successfully.');
    }

    public function show(OkrCheckIn $checkIn)
    {
        // Verify user can view this check-in
        if ($checkIn->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $checkIn->load(['objective', 'keyResult', 'user']);

        return view('okr-check-ins.show', compact('checkIn'));
    }

    public function edit(OkrCheckIn $checkIn)
    {
        if ($checkIn->user_id !== auth()->id()) {
            abort(403);
        }

        $objectives = Objective::where(function($query) {
            $query->where('user_id', auth()->id())
                  ->orWhere('creator_id', auth()->id());
        })->with('keyResults')->get();

        return view('okr-check-ins.edit', compact('checkIn', 'objectives'));
    }

    public function update(Request $request, OkrCheckIn $checkIn)
    {
        if ($checkIn->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'confidence_level' => 'required|numeric|min:0|max:1',
            'progress_notes' => 'required|string',
            'challenges' => 'nullable|string',
            'next_steps' => 'required|string',
            'risk_factors' => 'nullable|array',
            'check_in_type' => 'required|in:weekly,bi_weekly,monthly,quarterly,ad_hoc'
        ]);

        $checkIn->update([
            'confidence_level' => $request->confidence_level,
            'progress_notes' => $request->progress_notes,
            'challenges' => $request->challenges,
            'next_steps' => $request->next_steps,
            'risk_factors' => $request->risk_factors ?? [],
            'check_in_type' => $request->check_in_type
        ]);

        return redirect()->route('okr-check-ins.show', $checkIn)
            ->with('success', 'Check-in updated successfully.');
    }

    public function destroy(OkrCheckIn $checkIn)
    {
        if ($checkIn->user_id !== auth()->id()) {
            abort(403);
        }

        $checkIn->delete();

        return redirect()->route('okr-check-ins.index')
            ->with('success', 'Check-in deleted successfully.');
    }

    public function quickCheckIn(Request $request)
    {
        $request->validate([
            'objective_id' => 'nullable|exists:objectives,id',
            'key_result_id' => 'nullable|exists:key_results,id',
            'confidence_level' => 'required|numeric|min:0|max:1',
            'progress_notes' => 'required|string|max:500',
        ]);

        // Get previous and current progress
        $previousProgress = 0;
        $currentProgress = 0;

        if ($request->objective_id) {
            $objective = Objective::findOrFail($request->objective_id);
            if ($objective->user_id !== auth()->id() && $objective->creator_id !== auth()->id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            $previousProgress = $objective->okr_score ?? 0;
            $currentProgress = $objective->calculateOkrScore();
        }

        if ($request->key_result_id) {
            $keyResult = KeyResult::findOrFail($request->key_result_id);
            if ($keyResult->owner_id !== auth()->id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            $previousProgress = $keyResult->okr_score ?? 0;
            $currentProgress = $keyResult->calculateOkrScore();
        }

        $checkIn = OkrCheckIn::create([
            'objective_id' => $request->objective_id,
            'key_result_id' => $request->key_result_id,
            'user_id' => auth()->id(),
            'previous_progress' => $previousProgress,
            'current_progress' => $currentProgress,
            'confidence_level' => $request->confidence_level,
            'progress_notes' => $request->progress_notes,
            'next_steps' => 'Continuing with current approach',
            'check_in_type' => 'ad_hoc',
            'check_in_date' => now()->toDateString()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Quick check-in recorded successfully',
            'check_in' => $checkIn->load(['objective', 'keyResult'])
        ]);
    }
} 