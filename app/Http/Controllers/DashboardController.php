<?php

namespace App\Http\Controllers;

use App\Actions\Dashboard\GetDashboardStatsAction;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        protected GetDashboardStatsAction $getDashboardStats,
    ) {}

    public function index(Request $request): Response
    {
        $dashboardData = $this->getDashboardStats->index($request->user());

        return Inertia::render('Dashboard', $dashboardData);
    }
}
