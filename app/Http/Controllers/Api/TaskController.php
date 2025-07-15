<?php

namespace App\Http\Controllers\Api;

use App\Models\Task;
use App\Models\KeyResult;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TaskController extends BaseController
{
    public function index(): JsonResponse
    {
        $tasks = Task::with(['creator', 'assignee', 'keyResult', 'acceptance'])
            ->when(!auth()->user()->hasRole('admin'), function ($query) {
                return $query->where('creator_id', auth()->id())
                    ->orWhere('assignee_id', auth()->id());
            })
            ->get();

        return $this->sendResponse($tasks, 'Tasks retrieved successfully.');
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'key_result_id' => 'nullable|exists:key_results,id',
            'assignee_id' => 'required|exists:users,id',
            'priority' => ['required', Rule::in(['low', 'medium', 'high', 'urgent'])],
            'due_date' => 'required|date|after:today',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
        }

        if ($request->key_result_id) {
            $keyResult = KeyResult::find($request->key_result_id);
            if (!auth()->user()->hasRole('admin') && 
                $keyResult->owner_id !== auth()->id() && 
                $keyResult->objective->user_id !== auth()->id()) {
                return $this->sendError('Unauthorized to create task for this Key Result.', [], 403);
            }
        }

        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'key_result_id' => $request->key_result_id,
            'creator_id' => auth()->id(),
            'assignee_id' => $request->assignee_id,
            'priority' => $request->priority,
            'status' => 'assigned',
            'due_date' => $request->due_date,
        ]);

        return $this->sendResponse(
            $task->load(['creator', 'assignee', 'keyResult']),
            'Task created successfully.'
        );
    }

    public function show(Task $task): JsonResponse
    {
        if (!auth()->user()->hasRole('admin') && 
            $task->creator_id !== auth()->id() && 
            $task->assignee_id !== auth()->id()) {
            return $this->sendError('Unauthorized.', [], 403);
        }

        return $this->sendResponse(
            $task->load(['creator', 'assignee', 'keyResult', 'acceptance', 'comments', 'attachments']),
            'Task retrieved successfully.'
        );
    }

    public function update(Request $request, Task $task): JsonResponse
    {
        if (!auth()->user()->hasRole('admin') && $task->creator_id !== auth()->id()) {
            return $this->sendError('Unauthorized.', [], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'key_result_id' => 'nullable|exists:key_results,id',
            'assignee_id' => 'required|exists:users,id',
            'priority' => ['required', Rule::in(['low', 'medium', 'high', 'urgent'])],
            'status' => ['required', Rule::in(['assigned', 'pending_acceptance', 'accepted', 'rejected', 'in_progress', 'completed', 'blocked'])],
            'due_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
        }

        if ($request->key_result_id && $request->key_result_id !== $task->key_result_id) {
            $keyResult = KeyResult::find($request->key_result_id);
            if (!auth()->user()->hasRole('admin') && 
                $keyResult->owner_id !== auth()->id() && 
                $keyResult->objective->user_id !== auth()->id()) {
                return $this->sendError('Unauthorized to assign task to this Key Result.', [], 403);
            }
        }

        $task->update($request->only([
            'title',
            'description',
            'key_result_id',
            'assignee_id',
            'priority',
            'status',
            'due_date',
        ]));

        return $this->sendResponse(
            $task->load(['creator', 'assignee', 'keyResult']),
            'Task updated successfully.'
        );
    }

    public function destroy(Task $task): JsonResponse
    {
        if (!auth()->user()->hasRole('admin') && $task->creator_id !== auth()->id()) {
            return $this->sendError('Unauthorized.', [], 403);
        }

        $task->delete();

        return $this->sendResponse([], 'Task deleted successfully.');
    }

    public function accept(Task $task): JsonResponse
    {
        if ($task->assignee_id !== auth()->id()) {
            return $this->sendError('Unauthorized.', [], 403);
        }

        if ($task->status !== 'assigned' && $task->status !== 'pending_acceptance') {
            return $this->sendError('Task cannot be accepted in its current state.', [], 422);
        }

        $task->accept();

        return $this->sendResponse(
            $task->load(['creator', 'assignee', 'acceptance']),
            'Task accepted successfully.'
        );
    }

    public function reject(Request $request, Task $task): JsonResponse
    {
        if ($task->assignee_id !== auth()->id()) {
            return $this->sendError('Unauthorized.', [], 403);
        }

        if ($task->status !== 'assigned' && $task->status !== 'pending_acceptance') {
            return $this->sendError('Task cannot be rejected in its current state.', [], 422);
        }

        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
        }

        $task->reject($request->reason);

        return $this->sendResponse(
            $task->load(['creator', 'assignee', 'acceptance']),
            'Task rejected successfully.'
        );
    }

    public function complete(Task $task): JsonResponse
    {
        if (!auth()->user()->hasRole('admin') && 
            $task->assignee_id !== auth()->id()) {
            return $this->sendError('Unauthorized.', [], 403);
        }

        if ($task->status !== 'in_progress' && $task->status !== 'accepted') {
            return $this->sendError('Task cannot be completed in its current state.', [], 422);
        }

        $task->complete();

        return $this->sendResponse(
            $task->load(['creator', 'assignee']),
            'Task marked as completed successfully.'
        );
    }
} 