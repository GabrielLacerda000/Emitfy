<?php

namespace App\Actions\Dashboard;

use App\Enums\InvoiceStatus;
use App\Models\User;

class GetDashboardStatsAction
{
    public function index(User $user): array
    {
        // COALESCE - return the first parameter if it's not null, otherwise return the second parameter

        // Calculate total outstanding (SENT + OVERDUE invoices)
        $totalOutstanding = $user->invoices()
            ->whereIn('status', [InvoiceStatus::SENT])
            ->sum('total');

        $totalPaidStats = $user->invoices()
            ->where('status', InvoiceStatus::PAID)
            ->selectRaw('COALESCE(SUM(total), 0) as total, COUNT(*) as count')
            ->first();

        // Calculate overdue stats (amount + count)
        $overdueStats = $user->invoices()
            ->where('status', InvoiceStatus::OVERDUE)
            ->selectRaw('COALESCE(SUM(total), 0) as total, COUNT(*) as count')
            ->first();

        // Calculate due soon stats (SENT invoices due in next 7 days)
        $dueSoonStats = $user->invoices()
            ->where('status', InvoiceStatus::SENT)
            ->whereBetween('due_date', [now()->startOfDay(), now()->addDays(7)->endOfDay()])
            ->selectRaw('COALESCE(SUM(total), 0) as total, COUNT(*) as count')
            ->first();

        // Fetch 5 most recent invoices with client data
        $recentInvoices = $user->invoices()
            ->with('client:id,name,email,company_name')
            ->latest()
            ->limit(5)
            ->get();

        // Fetch 5 most recent clients with invoice counts
        $recentClients = $user->clients()
            ->withCount('invoices')
            ->latest()
            ->limit(5)
            ->get();

        return [
            'stats' => [
                'totalOutstanding' => number_format($totalOutstanding, 2, '.', ''),
                'totalPaid' => number_format($totalPaidStats->total ?? 0, 2, '.', ''),
                'totalPaidCount' => $totalPaidStats->count ?? 0,
                'totalOverdue' => number_format($overdueStats->total ?? 0, 2, '.', ''),
                'overdueCount' => $overdueStats->count ?? 0,
                'dueSoonCount' => $dueSoonStats->count ?? 0,
                'dueSoonTotal' => number_format($dueSoonStats->total ?? 0, 2, '.', ''),
            ],
            'recentInvoices' => $recentInvoices,
            'recentClients' => $recentClients,
            'monthlyRevenue' => $this->getMonthlyRevenue($user),
        ];
    }

    private function getMonthlyRevenue(User $user): array
    {
        $sixMonthsAgo = now()->subMonths(5)->startOfMonth();

        // Fetch paid invoices from last 6 months (database-agnostic)
        $paidInvoices = $user->invoices()
            ->where('status', InvoiceStatus::PAID)
            ->where('paid_at', '>=', $sixMonthsAgo)
            ->get(['paid_at', 'total']);

        // Group by month in PHP using Carbon
        $grouped = $paidInvoices->groupBy(function ($invoice) {
            return $invoice->paid_at->format('Y-m');
        })->map(function ($group) {
            return $group->sum('total');
        });

        // Build 6-month array (fill missing months with 0)
        $months = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthKey = $date->format('Y-m');
            $monthLabel = $date->format('M Y');

            $months[] = $monthLabel;
            $data[] = $grouped->get($monthKey, 0);
        }

        return [
            'labels' => $months,
            'data' => $data,
        ];
    }
}
