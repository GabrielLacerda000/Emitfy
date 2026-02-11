<script setup lang="ts">
import {
    CheckCircle,
    Clock,
    FileText,
    TrendingUp,
} from 'lucide-vue-next';
import { Card } from '@/components/ui/card';
import { formatBRL } from '@/lib/utils';
import type { ClientStats } from '@/types';

interface Props {
    stats: ClientStats;
}

defineProps<Props>();
</script>

<template>
    <div class="grid gap-4 md:grid-cols-4">
        <!-- Total Paid -->
        <Card class="p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">
                        Total Paid
                    </p>
                    <p class="mt-2 text-2xl font-bold">
                        {{ formatBRL(stats.totalPaid) }}
                    </p>
                    <p class="mt-1 text-xs text-muted-foreground">
                        {{ stats.totalPaidCount }}
                        {{
                            stats.totalPaidCount === 1 ? 'invoice' : 'invoices'
                        }}
                    </p>
                </div>
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-full bg-green-500/10"
                >
                    <CheckCircle
                        class="h-6 w-6 text-green-600 dark:text-green-500"
                    />
                </div>
            </div>
        </Card>

        <!-- Total Pending -->
        <Card class="p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">
                        Total Pending
                    </p>
                    <p class="mt-2 text-2xl font-bold">
                        {{ formatBRL(stats.totalPending) }}
                    </p>
                    <p class="mt-1 text-xs text-muted-foreground">
                        {{ stats.totalPendingCount }}
                        {{
                            stats.totalPendingCount === 1
                                ? 'invoice'
                                : 'invoices'
                        }}
                    </p>
                </div>
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-500/10"
                >
                    <Clock class="h-6 w-6 text-blue-600 dark:text-blue-500" />
                </div>
            </div>
        </Card>

        <!-- Total Overdue -->
        <Card class="p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">
                        Total Overdue
                    </p>
                    <p class="mt-2 text-2xl font-bold">
                        {{ formatBRL(stats.totalOverdue) }}
                    </p>
                    <p class="mt-1 text-xs text-muted-foreground">
                        {{ stats.totalOverdueCount }}
                        {{
                            stats.totalOverdueCount === 1
                                ? 'invoice'
                                : 'invoices'
                        }}
                    </p>
                </div>
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-full bg-red-500/10"
                >
                    <TrendingUp
                        class="h-6 w-6 text-red-600 dark:text-red-500"
                    />
                </div>
            </div>
        </Card>

        <!-- Total Draft -->
        <Card class="p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">
                        Total Draft
                    </p>
                    <p class="mt-2 text-2xl font-bold">
                        {{ formatBRL(stats.totalDraft) }}
                    </p>
                    <p class="mt-1 text-xs text-muted-foreground">
                        {{ stats.totalDraftCount }}
                        {{
                            stats.totalDraftCount === 1 ? 'invoice' : 'invoices'
                        }}
                    </p>
                </div>
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-500/10"
                >
                    <FileText
                        class="h-6 w-6 text-gray-600 dark:text-gray-500"
                    />
                </div>
            </div>
        </Card>
    </div>
</template>
