<?php

namespace App\Http\Controllers\Api;

use App\Models\KeyResult;
use App\Models\Objective;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class KeyResultController extends BaseController
{
    public function index(Objective $objective): JsonResponse
    {
        if (!auth()->user()->hasRole('admin') && 
            $objective->user_id !== auth()->id() && 
            !$objective->team?->members()->where('user_id', auth()->id())->exists()) {
            return $this->sendError('Unauthorized.', [], 403);
        }

        $keyResults = $objective->keyResults()->with(['owner', 'tasks'])->get();

        return $this->sendResponse($keyResults, 'Key Results retrieved successfully.');
    }

    public function store(Request $request, Objective $objective): JsonResponse
    {
        if (!auth()->user()->hasRole('admin') && 
            $objective->user_id !== auth()->id() && 
            !$objective->team?->members()->where('user_id', auth()->id())->exists()) {
            return $this->sendError('Unauthorized.', [], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'owner_id' => 'required|exists:users,id',
            'target_value' => 'required|numeric|min:0',
            'current_value' => 'required|numeric|min:0',
            'metric_unit' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
        }

        $keyResult = $objective->keyResults()->create([
            'title' => $request->title,
            'description' => $request->description,
            'owner_id' => $request->owner_id,
            'target_value' => $request->target_value,
            'current_value' => $request->current_value,
            'metric_unit' => $request->metric_unit,
            'status' => 'not_started',
        ]);

        $keyResult->calculateProgress();

        return $this->sendResponse(
            $keyResult->load(['owner', 'objective']),
            'Key Result created successfully.'
        );
    }

    public function show(KeyResult $keyResult): JsonResponse
    {
        if (!auth()->user()->hasRole('admin') && 
            $keyResult->objective->user_id !== auth()->id() && 
            !$keyResult->objective->team?->members()->where('user_id', auth()->id())->exists()) {
            return $this->sendError('Unauthorized.', [], 403);
        }

        return $this->sendResponse(
            $keyResult->load(['owner', 'objective', 'tasks']),
            'Key Result retrieved successfully.'
        );
    }

    public function update(Request $request, KeyResult $keyResult): JsonResponse
    {
        if (!auth()->user()->hasRole('admin') && 
            $keyResult->objective->user_id !== auth()->id() && 
            !$keyResult->objective->team?->members()->where('user_id', auth()->id())->exists()) {
            return $this->sendError('Unauthorized.', [], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'owner_id' => 'required|exists:users,id',
            'target_value' => 'required|numeric|min:0',
            'current_value' => 'required|numeric|min:0',
            'metric_unit' => 'nullable|string|max:50',
            'status' => ['required', Rule::in(['not_started', 'in_progress', 'completed', 'blocked'])],
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
        }

        $keyResult->update($request->only([
            'title',
            'description',
            'owner_id',
            'target_value',
            'current_value',
            'metric_unit',
            'status',
        ]));

        $keyResult->calculateProgress();

        return $this->sendResponse(
            $keyResult->load(['owner', 'objective']),
            'Key Result updated successfully.'
        );
    }

    public function destroy(KeyResult $keyResult): JsonResponse
    {
        if (!auth()->user()->hasRole('admin') && 
            $keyResult->objective->user_id !== auth()->id()) {
            return $this->sendError('Unauthorized.', [], 403);
        }

        $keyResult->delete();

        return $this->sendResponse([], 'Key Result deleted successfully.');
    }

    public function updateProgress(KeyResult $keyResult, Request $request)
    {
        $request->validate([
            'progress' => ['required', 'integer', 'min:0', 'max:100']
        ]);

        $targetValue = $keyResult->target_value;
        $newCurrentValue = ($request->progress / 100) * $targetValue;

        $keyResult->current_value = $newCurrentValue;
        $keyResult->calculateProgress();

        return response()->json(['success' => true]);
    }
} 