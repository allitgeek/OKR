<?php

namespace App\Http\Controllers\Api;

use App\Models\Objective;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ObjectiveController extends BaseController
{
    public function index(): JsonResponse
    {
        $objectives = Objective::with(['user', 'team', 'keyResults', 'categories'])
            ->when(auth()->user()->hasRole('admin'), function ($query) {
                return $query;
            }, function ($query) {
                return $query->where('user_id', auth()->id())
                    ->orWhereHas('team', function ($q) {
                        $q->whereHas('members', function ($q) {
                            $q->where('user_id', auth()->id());
                        });
                    });
            })
            ->get();

        return $this->sendResponse($objectives, 'Objectives retrieved successfully.');
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'team_id' => 'nullable|exists:teams,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'time_period' => ['required', Rule::in(['monthly', 'quarterly', 'yearly'])],
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
        }

        $objective = Objective::create([
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => auth()->id(),
            'team_id' => $request->team_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'time_period' => $request->time_period,
            'status' => 'not_started',
        ]);

        if ($request->has('categories')) {
            $objective->categories()->sync($request->categories);
        }

        return $this->sendResponse($objective->load(['user', 'team', 'categories']), 'Objective created successfully.');
    }

    public function show(Objective $objective): JsonResponse
    {
        if (!auth()->user()->hasRole('admin') && 
            $objective->user_id !== auth()->id() && 
            !$objective->team?->members()->where('user_id', auth()->id())->exists()) {
            return $this->sendError('Unauthorized.', [], 403);
        }

        return $this->sendResponse(
            $objective->load(['user', 'team', 'keyResults', 'categories']),
            'Objective retrieved successfully.'
        );
    }

    public function update(Request $request, Objective $objective): JsonResponse
    {
        if (!auth()->user()->hasRole('admin') && 
            $objective->user_id !== auth()->id() && 
            !$objective->team?->members()->where('user_id', auth()->id())->exists()) {
            return $this->sendError('Unauthorized.', [], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'team_id' => 'nullable|exists:teams,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'time_period' => ['required', Rule::in(['monthly', 'quarterly', 'yearly'])],
            'status' => ['required', Rule::in(['not_started', 'in_progress', 'completed', 'archived'])],
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
        }

        $objective->update($request->only([
            'title',
            'description',
            'team_id',
            'start_date',
            'end_date',
            'time_period',
            'status',
        ]));

        if ($request->has('categories')) {
            $objective->categories()->sync($request->categories);
        }

        return $this->sendResponse(
            $objective->load(['user', 'team', 'keyResults', 'categories']),
            'Objective updated successfully.'
        );
    }

    public function destroy(Objective $objective): JsonResponse
    {
        if (!auth()->user()->hasRole('admin') && $objective->user_id !== auth()->id()) {
            return $this->sendError('Unauthorized.', [], 403);
        }

        $objective->delete();

        return $this->sendResponse([], 'Objective deleted successfully.');
    }
} 