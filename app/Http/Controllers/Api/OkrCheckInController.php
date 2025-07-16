<?php

namespace App\Http\Controllers\Api;

use App\Models\{OkrCheckIn, Objective, KeyResult};
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class OkrCheckInController extends BaseController
{
    public function index(Request $request): JsonResponse
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

        if ($request->filled('objective_id')) {
            $query->where('objective_id', $request->objective_id);
        }

        if ($request->filled('key_result_id')) {
            $query->where('key_result_id', $request->key_result_id);
        }

        $checkIns = $query->latest('check_in_date')->paginate(15);

        return $this->sendResponse($checkIns, 'Check-ins retrieved successfully.');
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
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

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
        }

        // Validate ownership
        if ($request->objective_id) {
            $objective = Objective::findOrFail($request->objective_id);
            if ($objective->user_id !== auth()->id() && $objective->creator_id !== auth()->id()) {
                return $this->sendError('Unauthorized.', [], 403);
            }
        }

        if ($request->key_result_id) {
            $keyResult = KeyResult::findOrFail($request->key_result_id);
            if ($keyResult->owner_id !== auth()->id()) {
                return $this->sendError('Unauthorized.', [], 403);
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

        return $this->sendResponse(
            $checkIn->load(['objective', 'keyResult', 'user']),
            'Check-in recorded successfully.',
            201
        );
    }

    public function show(OkrCheckIn $checkIn): JsonResponse
    {
        if ($checkIn->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            return $this->sendError('Unauthorized.', [], 403);
        }

        $checkIn->load(['objective', 'keyResult', 'user']);

        return $this->sendResponse($checkIn, 'Check-in retrieved successfully.');
    }

    public function update(Request $request, OkrCheckIn $checkIn): JsonResponse
    {
        if ($checkIn->user_id !== auth()->id()) {
            return $this->sendError('Unauthorized.', [], 403);
        }

        $validator = Validator::make($request->all(), [
            'confidence_level' => 'required|numeric|min:0|max:1',
            'progress_notes' => 'required|string',
            'challenges' => 'nullable|string',
            'next_steps' => 'required|string',
            'risk_factors' => 'nullable|array',
            'check_in_type' => 'required|in:weekly,bi_weekly,monthly,quarterly,ad_hoc'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
        }

        $checkIn->update([
            'confidence_level' => $request->confidence_level,
            'progress_notes' => $request->progress_notes,
            'challenges' => $request->challenges,
            'next_steps' => $request->next_steps,
            'risk_factors' => $request->risk_factors ?? [],
            'check_in_type' => $request->check_in_type
        ]);

        return $this->sendResponse(
            $checkIn->load(['objective', 'keyResult', 'user']),
            'Check-in updated successfully.'
        );
    }

    public function destroy(OkrCheckIn $checkIn): JsonResponse
    {
        if ($checkIn->user_id !== auth()->id()) {
            return $this->sendError('Unauthorized.', [], 403);
        }

        $checkIn->delete();

        return $this->sendResponse([], 'Check-in deleted successfully.');
    }

    public function quickCheckIn(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'objective_id' => 'nullable|exists:objectives,id',
            'key_result_id' => 'nullable|exists:key_results,id',
            'confidence_level' => 'required|numeric|min:0|max:1',
            'progress_notes' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
        }

        // Validate ownership
        if ($request->objective_id) {
            $objective = Objective::findOrFail($request->objective_id);
            if ($objective->user_id !== auth()->id() && $objective->creator_id !== auth()->id()) {
                return $this->sendError('Unauthorized.', [], 403);
            }
        }

        if ($request->key_result_id) {
            $keyResult = KeyResult::findOrFail($request->key_result_id);
            if ($keyResult->owner_id !== auth()->id()) {
                return $this->sendError('Unauthorized.', [], 403);
            }
        }

        // Get previous and current progress
        $previousProgress = 0;
        $currentProgress = 0;

        if ($request->objective_id) {
            $objective = Objective::findOrFail($request->objective_id);
            $previousProgress = $objective->okr_score ?? 0;
            $currentProgress = $objective->calculateOkrScore();
        }

        if ($request->key_result_id) {
            $keyResult = KeyResult::findOrFail($request->key_result_id);
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

        return $this->sendResponse(
            $checkIn->load(['objective', 'keyResult']),
            'Quick check-in recorded successfully.',
            201
        );
    }

    public function analytics(Request $request): JsonResponse
    {
        $period = $request->get('period', 'month');
        
        $startDate = match($period) {
            'week' => Carbon::now()->subWeek(),
            'month' => Carbon::now()->subMonth(),
            'quarter' => Carbon::now()->subQuarter(),
            'year' => Carbon::now()->subYear(),
            default => Carbon::now()->subMonth()
        };

        $checkIns = OkrCheckIn::where('user_id', auth()->id())
            ->where('check_in_date', '>=', $startDate)
            ->with(['objective', 'keyResult'])
            ->get();

        $analytics = [
            'total_check_ins' => $checkIns->count(),
            'average_confidence' => $checkIns->avg('confidence_level'),
            'confidence_trend' => $this->getConfidenceTrend($checkIns),
            'check_in_frequency' => $checkIns->groupBy('check_in_type')->map->count(),
            'progress_improvements' => $checkIns->filter(fn($c) => $c->getProgressDelta() > 0)->count(),
            'at_risk_count' => $checkIns->filter(fn($c) => $c->getRiskLevel() === 'high')->count()
        ];

        return $this->sendResponse($analytics, 'Check-in analytics retrieved successfully.');
    }

    private function getConfidenceTrend($checkIns): array
    {
        return $checkIns->sortBy('check_in_date')
            ->groupBy(fn($item) => $item->check_in_date->format('Y-m-d'))
            ->map(fn($group) => [
                'date' => $group->first()->check_in_date->format('Y-m-d'),
                'confidence' => $group->avg('confidence_level')
            ])
            ->values()
            ->toArray();
    }
} 