<?php

namespace App\Http\Controllers;

use App\Services\DashboardWorkflowService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardWorkflowService $workflowService
    ) {}

    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $user?->load('roles');

        $totalEnrolments = \App\Models\Enrolment::count();
        $completedEnrolments = \App\Models\Enrolment::whereNotNull('completed_at')->count();
        $activeLearners = \App\Models\User::whereHas('enrolments')->count();
        $assessmentAttempts = \App\Models\AssessmentAttempt::count();
        $certificatesIssued = \App\Models\Certificate::count();

        $workflowPanels = $user ? $this->workflowService->getWorkflowPanelsForUser($user) : [];
        $workflowAlerts = $user ? $this->workflowService->getAlertLinesForUser($user) : [];
        $pendingCounts = $this->workflowService->getPendingCounts();

        return view('dashboard', compact(
            'totalEnrolments',
            'completedEnrolments',
            'activeLearners',
            'assessmentAttempts',
            'certificatesIssued',
            'workflowPanels',
            'workflowAlerts',
            'pendingCounts'
        ));
    }
}
