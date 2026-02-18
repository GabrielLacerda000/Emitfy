<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    TrendingUp,
    Clock,
    AlertCircle,
    Plus,
} from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';
import RecentClientsList from '@/components/dashboard/RecentClientsList.vue';
import RecentInvoicesList from '@/components/dashboard/RecentInvoicesList.vue';
import RevenueChart from '@/components/dashboard/RevenueChart.vue';
import DashboardStatCard from '@/components/DashboardStatCard.vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { useFormatCurrency } from '@/composables/useLocale';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { index as clientsIndexRoute } from '@/routes/clients';
import { create as createInvoiceRoute, index as invoicesIndexRoute } from '@/routes/invoices';
import { type BreadcrumbItem, type DashboardData } from '@/types';

const { t } = useI18n();
const formatMoney = useFormatCurrency();

type Props = {
    stats: DashboardData['stats'];
    recentInvoices: DashboardData['recentInvoices'];
    recentClients: DashboardData['recentClients'];
    monthlyRevenue: DashboardData['monthlyRevenue'];
}

const props = defineProps<Props>();

    console.log('monthlyRevenue', props.monthlyRevenue)

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const createInvoiceUrl = createInvoiceRoute().url;
const invoicesIndexUrl = invoicesIndexRoute().url;
const clientsIndexUrl = clientsIndexRoute().url;
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-2 md:p-4">
            <!-- Header with title and action button -->
            <div class="flex items-center justify-between">
                <Heading
                    :title="t('dashboard.title')"
                    :description="t('dashboard.description')"
                />
                <Button as-child>
                    <Link :href="createInvoiceUrl">
                        <Plus class="mr-2 h-4 w-4" />
                        {{ t('dashboard.createInvoice') }}
                    </Link>
                </Button>
            </div>

            <!-- Stat Cards Grid -->
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <DashboardStatCard
                    :title="t('dashboard.stats.totalOutstanding')"
                    :amount="formatMoney(props.stats.totalOutstanding)"
                    :description="t('dashboard.stats.totalOutstandingDesc')"
                    :icon="TrendingUp"
                    icon-class="bg-blue-100 text-blue-600 dark:bg-blue-950 dark:text-blue-400"
                />
                <DashboardStatCard
                    :title="t('dashboard.stats.totalPaid')"
                    :amount="formatMoney(props.stats.totalPaid)"
                    :description="t('dashboard.stats.totalPaidDesc', { count: props.stats.totalPaidCount })"
                    :icon="TrendingUp"
                    icon-class="bg-green-100 text-green-600 dark:bg-green-950 dark:text-green-400"
                />
                <DashboardStatCard
                    :title="t('dashboard.stats.dueSoon')"
                    :amount="formatMoney(props.stats.dueSoonTotal)"
                    :description="t('dashboard.stats.dueSoonDesc', { count: props.stats.dueSoonCount })"
                    :icon="Clock"
                    icon-class="bg-amber-100 text-amber-600 dark:bg-amber-950 dark:text-amber-400"
                />
                <DashboardStatCard
                    :title="t('dashboard.stats.overdue')"
                    :amount="formatMoney(props.stats.totalOverdue)"
                    :description="t('dashboard.stats.overdueDesc', { count: props.stats.overdueCount })"
                    :icon="AlertCircle"
                    icon-class="bg-red-100 text-red-600 dark:bg-red-950 dark:text-red-400"
                />
            </div>

            <!-- Revenue Chart -->
            <RevenueChart :data="props.monthlyRevenue" />

            <!-- Recent Data Section -->
            <div class="grid gap-4 md:grid-cols-2">
                <RecentInvoicesList
                    :invoices="recentInvoices"
                    :view-all-url="invoicesIndexUrl"
                    :create-invoice-url="createInvoiceUrl"
                />

                <RecentClientsList
                    :clients="recentClients"
                    :view-all-url="clientsIndexUrl"
                    :manage-clients-url="clientsIndexUrl"
                />
            </div>
        </div>
    </AppLayout>
</template>
