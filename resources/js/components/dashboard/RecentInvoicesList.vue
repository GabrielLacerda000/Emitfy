<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowRight, FileText, Plus } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { getStatusConfig } from '@/composables/useInvoiceStatus';
import { formatBRL, formatDate } from '@/lib/utils';
import type { Invoice } from '@/types';

type Props = {
    invoices: Invoice[];
    viewAllUrl: string;
    createInvoiceUrl: string;
};

defineProps<Props>();
</script>

<template>
    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold">Recent Invoices</h3>
            <Button variant="ghost" size="sm" as-child>
                <Link :href="viewAllUrl">
                    View all
                    <ArrowRight class="ml-2 h-4 w-4" />
                </Link>
            </Button>
        </div>

        <!-- Table or Empty State -->
        <div
            v-if="invoices.length > 0"
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
                        v-for="invoice in invoices"
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
</template>
