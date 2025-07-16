<?php

namespace App\Http\Controllers\Api;

use App\Models\OkrCycle;
use App\Services\OkrService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OkrCycleController extends BaseController
{
    protected OkrService $okrService;

    public function __construct(OkrService $okrService)
    {
        $this->okrService = $okrService;
    }

    public function index(): JsonResponse
    {
        $cycles = OkrCycle::with(['objectives'])
            ->orderBy('year', 'desc')
            ->orderBy('quarter', 'desc')
            ->paginate(20);

        $currentCycle = OkrCycle::getCurrent();
        $activeCycle = OkrCycle::active()->first();

        return $this->sendResponse([
            'cycles' => $cycles,
            'current_cycle' => $currentCycle,
            'active_cycle' => $activeCycle
        ], 'OKR Cycles retrieved successfully.');
    }

    public function show(OkrCycle $cycle): JsonResponse
    {
        $cycle->load(['objectives.keyResults', 'objectives.user']);
        $healthReport = $this->okrService->getCycleHealthReport($cycle);
        
        return $this->sendResponse([
            'cycle' => $cycle,
            'health_report' => $healthReport
        ], 'OKR Cycle retrieved successfully.');
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'year' => 'required|integer|min:2020|max:2030',
            'quarter' => 'required|integer|min:1|max:4',
            'description' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray(), 422);
        }

        $existing = OkrCycle::where('year', $request->year)
            ->where('quarter', $request->quarter)
            ->first();

        if ($existing) {
            return $this->sendError('Cycle already exists for this year and quarter.', [], 409);
        }

        $cycle = $this->okrService->createCycle($request->year, $request->quarter);
        
        if ($request->description) {
            $cycle->update(['description' => $request->description]);
        }

        return $this->sendResponse($cycle, 'OKR Cycle created successfully.', 201);
    }

    public function startCycle(OkrCycle $cycle): JsonResponse
    {
        if ($cycle->status === 'active') {
            return $this->sendError('This cycle is already active.', [], 400);
        }

        $this->okrService->startCycle($cycle);

        return $this->sendResponse($cycle->fresh(), 'Cycle started successfully.');
    }

    public function healthReport(): JsonResponse
    {
        $healthReport = $this->okrService->getOverallHealthReport();
        
        return $this->sendResponse($healthReport, 'Health report retrieved successfully.');
    }
} 