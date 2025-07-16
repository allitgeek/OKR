<?php

namespace App\Http\Controllers;

use App\Models\OkrCycle;
use App\Services\OkrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class OkrCycleController extends Controller
{
    protected OkrService $okrService;

    public function __construct(OkrService $okrService)
    {
        $this->okrService = $okrService;
        $this->middleware('auth');
    }

    public function index()
    {
        $cycles = OkrCycle::with(['objectives'])
            ->orderBy('year', 'desc')
            ->orderBy('quarter', 'desc')
            ->paginate(12);

        $currentCycle = OkrCycle::getCurrent();
        $activeCycle = OkrCycle::active()->first();

        return view('okr-cycles.index', compact('cycles', 'currentCycle', 'activeCycle'));
    }

    public function show(OkrCycle $cycle)
    {
        $cycle->load(['objectives.keyResults', 'objectives.user']);
        
        $healthReport = $this->okrService->getCycleHealthReport($cycle);
        
        return view('okr-cycles.show', compact('cycle', 'healthReport'));
    }

    public function create()
    {
        return view('okr-cycles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2020|max:2030',
            'quarter' => 'required|integer|min:1|max:4',
            'description' => 'nullable|string|max:500'
        ]);

        // Check if cycle already exists
        $existing = OkrCycle::where('year', $request->year)
            ->where('quarter', $request->quarter)
            ->first();

        if ($existing) {
            return back()->withErrors(['quarter' => 'This cycle already exists.']);
        }

        $cycle = $this->okrService->createCycle($request->year, $request->quarter);
        
        if ($request->description) {
            $cycle->update(['description' => $request->description]);
        }

        return redirect()->route('okr-cycles.show', $cycle)
            ->with('success', 'OKR Cycle created successfully.');
    }

    public function edit(OkrCycle $cycle)
    {
        return view('okr-cycles.edit', compact('cycle'));
    }

    public function update(Request $request, OkrCycle $cycle)
    {
        $request->validate([
            'description' => 'nullable|string|max:500',
            'planning_start' => 'nullable|date',
            'mid_quarter_review' => 'nullable|date',
            'scoring_deadline' => 'nullable|date|after:end_date'
        ]);

        $cycle->update($request->only(['description', 'planning_start', 'mid_quarter_review', 'scoring_deadline']));

        return redirect()->route('okr-cycles.show', $cycle)
            ->with('success', 'OKR Cycle updated successfully.');
    }

    public function startCycle(OkrCycle $cycle)
    {
        if ($cycle->status === 'active') {
            return back()->with('warning', 'This cycle is already active.');
        }

        $this->okrService->startCycle($cycle);

        return back()->with('success', "Cycle {$cycle->name} has been started successfully.");
    }

    public function closeCycle(OkrCycle $cycle)
    {
        if ($cycle->status !== 'active') {
            return back()->with('error', 'Only active cycles can be closed.');
        }

        $this->okrService->closeCycle($cycle);

        return back()->with('success', "Cycle {$cycle->name} has been closed and final scores calculated.");
    }

    public function initializeYear(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2020|max:2030'
        ]);

        $year = $request->year;
        
        // Check if cycles already exist
        if (OkrCycle::forYear($year)->exists()) {
            return back()->withErrors(['year' => "Cycles for year {$year} already exist."]);
        }

        $cycles = $this->okrService->initializeYear($year);

        return redirect()->route('okr-cycles.index')
            ->with('success', "Successfully initialized {$cycles->count()} cycles for year {$year}.");
    }

    public function dashboard()
    {
        try {
            $currentCycle = $this->okrService->getCurrentCycle();
            $healthReport = $this->okrService->getOverallHealthReport();
            
            // Get recent check-ins
            $recentCheckIns = collect();
            if ($currentCycle) {
                $recentCheckIns = $currentCycle->checkIns()
                    ->with(['objective', 'keyResult', 'user'])
                    ->recent(7)
                    ->latest()
                    ->limit(10)
                    ->get();
            }

            return view('okr-cycles.dashboard', compact('currentCycle', 'healthReport', 'recentCheckIns'));
        } catch (\Exception $e) {
            // Log the error and show a more user-friendly message
            Log::error('OKR Dashboard Error: ' . $e->getMessage());
            
            // Provide fallback data
            $currentCycle = null;
            $healthReport = [
                'total_objectives' => 0,
                'completed_objectives' => 0,
                'success_rate' => 0,
                'average_score' => 0,
                'average_confidence' => 0.5
            ];
            $recentCheckIns = collect();
            
            return view('okr-cycles.dashboard', compact('currentCycle', 'healthReport', 'recentCheckIns'))
                ->with('error', 'There was an issue loading the dashboard. Please ensure OKR cycles are properly initialized.');
        }
    }
} 