<?php

namespace App\Http\Controllers;

use App\Models\KeyResult;
use App\Models\Objective;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KeyResultController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(KeyResult::class, 'keyResult');
    }

    public function index()
    {
        $keyResults = KeyResult::where('owner_id', Auth::id())->paginate(10);
        return view('key-results.index', compact('keyResults'));
    }

    public function create()
    {
        $objectives = Objective::where('user_id', Auth::id())->get();
        $users = \App\Models\User::where('is_active', true)->get();
        return view('key-results.create', compact('objectives', 'users'));
    }

    public function store(Request $request)
    {
        $objective = Objective::findOrFail($request->objective_id);
        
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'objective_id' => ['required', 'exists:objectives,id'],
            'owner_id' => ['required', 'exists:users,id'],
            'target_value' => ['required', 'numeric', 'min:0'],
            'current_value' => ['required', 'numeric', 'min:0'],
            'metric_unit' => ['nullable', 'string', 'max:50'],
            'kr_type' => ['required', 'in:positive,negative,baseline,milestone'],
            'confidence_level' => ['required', 'numeric', 'min:0', 'max:1'],
            'start_date' => [
                'required', 
                'date',
                'after_or_equal:' . $objective->start_date->format('Y-m-d'),
                'before_or_equal:' . $objective->end_date->format('Y-m-d')
            ],
            'due_date' => [
                'required',
                'date',
                'after_or_equal:start_date',
                'before_or_equal:' . $objective->end_date->format('Y-m-d')
            ]
        ]);

        $keyResult = KeyResult::create($request->all());
        $keyResult->calculateProgress();

        return redirect()->route('objectives.show', $keyResult->objective)
            ->with('success', 'Key Result created successfully.');
    }

    public function show(KeyResult $keyResult)
    {
        return view('key-results.show', compact('keyResult'));
    }

    public function edit(KeyResult $keyResult)
    {
        $objectives = Objective::where('user_id', Auth::id())->get();
        return view('key-results.edit', compact('keyResult', 'objectives'));
    }

    public function update(Request $request, KeyResult $keyResult)
    {
        try {
            $validated = $request->validate([
                'title' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'target_value' => ['required', 'numeric', 'min:0'],
                'current_value' => ['required', 'numeric', 'min:0'],
                'start_date' => [
                    'required', 
                    'date',
                    'after_or_equal:' . $keyResult->objective->start_date->format('Y-m-d'),
                    'before_or_equal:' . $keyResult->objective->end_date->format('Y-m-d')
                ],
                'due_date' => [
                    'required',
                    'date',
                    'after_or_equal:start_date',
                    'before_or_equal:' . $keyResult->objective->end_date->format('Y-m-d')
                ]
            ]);

            DB::transaction(function () use ($keyResult, $validated) {
                $keyResult->update($validated);
                $keyResult->calculateProgress();
            });

            return redirect()
                ->route('objectives.show', $keyResult->objective_id)
                ->with('success', 'Key Result updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update key result. ' . $e->getMessage());
        }
    }

    public function destroy(KeyResult $keyResult)
    {
        $objectiveId = $keyResult->objective_id;
        $keyResult->delete();

        return redirect()->route('objectives.show', $objectiveId)
            ->with('success', 'Key Result deleted successfully.');
    }

    public function updateProgress(Request $request, KeyResult $keyResult)
    {
        try {
            $validated = $request->validate([
                'current_value' => ['required', 'numeric', 'min:0', 'max:' . $keyResult->target_value],
            ]);

            DB::transaction(function () use ($keyResult, $validated) {
                $keyResult->current_value = $validated['current_value'];
                $keyResult->save();
                $keyResult->calculateProgress();
            });

            return redirect()
                ->back()
                ->with('success', 'Progress updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update progress. ' . $e->getMessage());
        }
    }

    public function markComplete(KeyResult $keyResult)
    {
        $this->authorize('update', $keyResult);

        $keyResult->update([
            'current_value' => $keyResult->target_value,
            'status' => 'completed'
        ]);

        return back()->with('success', 'Key Result marked as complete.');
    }
} 