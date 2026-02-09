<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    TrendingUp,
    Clock,
    AlertCircle,
    Plus,
    ArrowRight,
    FileText,
    Users,
    User,
} from 'lucide-vue-next';
import DashboardStatCard from '@/components/DashboardStatCard.vue';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { formatBRL, formatDate } from '@/lib/utils';
import { dashboard } from '@/routes';
import { index as clientsIndexRoute } from '@/routes/clients';
import { create as createInvoiceRoute, index as invoicesIndexRoute } from '@/routes/invoices';
import { type BreadcrumbItem, type DashboardData, type InvoiceStatus } from '@/types';

type Props = {
    stats: DashboardData['stats'];
    recentInvoices: DashboardData['recentInvoices'];
    recentClients: DashboardData['recentClients'];
}

type StatusConfig = {
    variant: 'secondary' | 'default' | 'outline' | 'destructive'
    label: string
    class?: string
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const createInvoiceUrl = createInvoiceRoute().url;
const invoicesIndexUrl = invoicesIndexRoute().url;
const clientsIndexUrl = clientsIndexRoute().url;

function getStatusConfig(status: InvoiceStatus): StatusConfig {
    const configs: Record<InvoiceStatus, StatusConfig> = {
        draft: { variant: 'secondary', label: 'Draft' },
        sent: { variant: 'default', label: 'Sent' },
        paid: {
            variant: 'outline',
            label: 'Paid',
            class: 'border-green-600 text-green-700 dark:border-green-500 dark:text-green-400',
        },
        overdue: { variant: 'destructive', label: 'Overdue' },
    };

    return configs[status];
}
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-2 md:p-4">
            <!-- Header with title and action button -->
            <div class="flex items-center justify-between">
                <Heading
                    title="Dashboard"
                    description="Welcome back. Here's your financial summary."
                />
                <Button as-child>
                    <Link :href="createInvoiceUrl">
                        <Plus class="mr-2 h-4 w-4" />
                        Create Invoice
                    </Link>
                </Button>
            </div>

            <!-- Stat Cards Grid -->
            <div class="grid gap-4 md:grid-cols-3">
                <DashboardStatCard
                    title="TOTAL OUTSTANDING"
                    :amount="formatBRL(props.stats.totalOutstanding)"
                    description="Sent and overdue invoices"
                    :icon="TrendingUp"
                    icon-class="bg-blue-100 text-blue-600 dark:bg-blue-950 dark:text-blue-400"
                />
                <DashboardStatCard
                    title="DUE SOON"
                    :amount="formatBRL(props.stats.dueSoonTotal)"
                    :description="`${props.stats.dueSoonCount} invoices due in the next 7 days`"
                    :icon="Clock"
                    icon-class="bg-amber-100 text-amber-600 dark:bg-amber-950 dark:text-amber-400"
                />
                <DashboardStatCard
                    title="OVERDUE"
                    :amount="formatBRL(props.stats.totalOverdue)"
                    :description="`${props.stats.overdueCount} invoices past due`"
                    :icon="AlertCircle"
                    icon-class="bg-red-100 text-red-600 dark:bg-red-950 dark:text-red-400"
                />
            </div>

            <!-- Recent Data Section -->
            <div class="grid gap-4 md:grid-cols-2">
                <!-- Recent Invoices Table -->
                <div class="flex flex-col gap-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold">Recent Invoices</h3>
                        <Button variant="ghost" size="sm" as-child>
                            <Link :href="invoicesIndexUrl">
                                View all
                                <ArrowRight class="ml-2 h-4 w-4" />
                            </Link>
                        </Button>
                    </div>

                    <!-- Table or Empty State -->
                    <div
                        v-if="recentInvoices.length > 0"
                        class="overflow-x-auto rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                    >
                        <table class="w-full min-w-[600px]">
                            <thead>
                                <tr
                                    class="border-b border-sidebar-border/70 bg-muted/30 dark:border-sidebar-border"
                                >
                                    <th class="p-3 text-left text-xs font-medium text-muted-foreground">
                                        INVOICE
                                    </th>
                                    <th class="p-3 text-left text-xs font-medium text-muted-foreground">
                                        CLIENT
                                    </th>
                                    <th class="p-3 text-left text-xs font-medium text-muted-foreground">
                                        STATUS
                                    </th>
                                    <th class="p-3 text-right text-xs font-medium text-muted-foreground">
                                        AMOUNT
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="invoice in recentInvoices"
                                    :key="invoice.id"
                                    class="border-b border-sidebar-border/70 transition-colors hover:bg-muted/50 last:border-0 dark:border-sidebar-border"
                                >
                                    <td class="p-3">
                                        <div class="font-medium">
                                            {{ invoice.number }}
                                        </div>
                                        <div class="text-xs text-muted-foreground">
                                            Due on {{ formatDate(invoice.due_date) }}
                                        </div>
                                    </td>
                                    <td class="p-3">
                                        <div class="font-medium">
                                            {{ invoice.client.name }}
                                        </div>
                                        <div class="text-xs text-muted-foreground">
                                            {{
                                                invoice.client.company_name ||
                                                invoice.client.email
                                            }}
                                        </div>
                                    </td>
                                    <td class="p-3">
                                        <Badge
                                            :variant="getStatusConfig(invoice.status).variant"
                                            :class="getStatusConfig(invoice.status).class"
                                        >
                                            {{ getStatusConfig(invoice.status).label }}
                                        </Badge>
                                    </td>
                                    <td class="p-3 text-right font-medium">
                                        {{ formatBRL(invoice.total) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Empty state -->
                    <div
                        v-else
                        class="flex flex-col items-center justify-center rounded-xl border border-dashed border-sidebar-border/70 p-8 text-center dark:border-sidebar-border"
                    >
                        <FileText class="h-12 w-12 text-muted-foreground" />
                        <h3 class="mt-4 text-lg font-medium">No invoices yet</h3>
                        <p class="mt-1 text-sm text-muted-foreground">
                            Start by creating your first invoice
                        </p>
                        <Button class="mt-4" as-child>
                            <Link :href="createInvoiceUrl">
                                <Plus class="mr-2 h-4 w-4" />
                                Create Invoice
                            </Link>
                        </Button>
                    </div>
                </div>

                <!-- Recent Clients List -->
                <div class="flex flex-col gap-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold">Recent Clients</h3>
                        <Button variant="ghost" size="sm" as-child>
                            <Link :href="clientsIndexUrl">
                                Manage Clients
                                <ArrowRight class="ml-2 h-4 w-4" />
                            </Link>
                        </Button>
                    </div>

                    <!-- Clients list or Empty State -->
                    <div v-if="recentClients.length > 0" class="flex flex-col gap-2">
                        <div
                            v-for="client in recentClients"
                            :key="client.id"
                            class="group flex items-center justify-between rounded-xl border border-sidebar-border/70 p-4 transition-colors hover:bg-muted/50 dark:border-sidebar-border"
                        >
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10"
                                >
                                    <User class="h-5 w-5 text-primary" />
                                </div>

                                <div>
                                    <div class="font-medium">{{ client.name }}</div>
                                    <div class="text-sm text-muted-foreground">
                                        {{ client.company_name || client.email }}
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                <span>{{ client.invoices_count }} invoices</span>
                                <ArrowRight
                                    class="h-4 w-4 transition-transform group-hover:translate-x-1"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Empty state -->
                    <div
                        v-else
                        class="flex flex-col items-center justify-center rounded-xl border border-dashed border-sidebar-border/70 p-8 text-center dark:border-sidebar-border"
                    >
                        <Users class="h-12 w-12 text-muted-foreground" />
                        <h3 class="mt-4 text-lg font-medium">No clients yet</h3>
                        <p class="mt-1 text-sm text-muted-foreground">
                            Add your first client to get started
                        </p>
                        <Button class="mt-4" as-child>
                            <Link :href="clientsIndexUrl">
                                <Plus class="mr-2 h-4 w-4" />
                                Add Client
                            </Link>
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
