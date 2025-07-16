<?php

namespace App\Http\Controllers;

use App\Models\KeyResult;
use App\Models\Objective;
use App\Models\User;
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

    public function create(Request $request)
    {
        $objective = Objective::findOrFail($request->query('objective_id'));
        $users = User::all();
        return view('key-results.create', compact('objective', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'objective_id' => 'required|exists:objectives,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'initial_value' => 'required|numeric',
            'target_value' => 'required|numeric',
            'weight' => 'required|numeric|min:0|max:1',
            'assignee_id' => 'nullable|exists:users,id',
        ]);

        $objective = Objective::findOrFail($request->objective_id);

        $keyResult = KeyResult::create([
            'objective_id' => $objective->id,
            'title' => $request->title,
            'description' => $request->description,
            'initial_value' => $request->initial_value,
            'target_value' => $request->target_value,
            'weight' => $request->weight,
            'current_value' => $request->initial_value,
            'assignee_id' => $request->assignee_id,
        ]);

        return redirect()->route('objectives.show', $objective)->with('success', 'Key Result created successfully.');
    }

    public function show(KeyResult $keyResult)
    {
        return view('key-results.show', compact('keyResult'));
    }

    public function edit(KeyResult $keyResult)
    {
        $users = User::all();
        return view('key-results.edit', compact('keyResult', 'users'));
    }

    public function update(Request $request, KeyResult $keyResult)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_value' => 'required|numeric',
            'current_value' => 'required|numeric',
            'weight' => 'required|numeric|min:0|max:1',
            'status' => 'required|string',
            'assignee_id' => 'nullable|exists:users,id',
        ]);

        $keyResult->update($request->all());

        return redirect()->route('objectives.show', $keyResult->objective_id)->with('success', 'Key Result updated successfully.');
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