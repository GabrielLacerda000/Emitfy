<?php

namespace App\Actions\Dashboard;

use App\Enums\InvoiceStatus;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class GetDashboardStatsAction
{
    public function index(User $user): array
    {
        // COALESCE - return the first parameter if it's not null, otherwise return the second parameter

        // Calculate total outstanding (SENT + OVERDUE invoices)
        $totalOutstanding = $user->invoices()
            ->whereIn('status', [InvoiceStatus::SENT, InvoiceStatus::OVERDUE])
            ->sum('total');

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
                'totalOverdue' => number_format($overdueStats->total ?? 0, 2, '.', ''),
                'overdueCount' => $overdueStats->count ?? 0,
                'dueSoonCount' => $dueSoonStats->count ?? 0,
                'dueSoonTotal' => number_format($dueSoonStats->total ?? 0, 2, '.', ''),
            ],
            'recentInvoices' => $recentInvoices,
            'recentClients' => $recentClients,
        ];
    }
}
