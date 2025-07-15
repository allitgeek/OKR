<?php

namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    private AnalyticsService $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
        $this->middleware('auth');
    }

    /**
     * Display the analytics dashboard
     */
    public function dashboard(): View
    {
        return view('analytics.dashboard');
    }

    /**
     * Display detailed analytics reports
     */
    public function reports(): View
    {
        return view('analytics.reports');
    }

    /**
     * Display team performance analytics
     */
    public function teamPerformance(): View
    {
        return view('analytics.team-performance');
    }

    /**
     * Display predictive insights
     */
    public function insights(): View
    {
        return view('analytics.insights');
    }
} 