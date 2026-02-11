<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Eye, FileText } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { getStatusConfig } from '@/composables/useInvoiceStatus';
import { formatBRL, formatDate } from '@/lib/utils';
import { show as showInvoice } from '@/routes/invoices';
import type { PaginatedInvoices } from '@/types';

interface Props {
    invoices: PaginatedInvoices;
}

defineProps<Props>();
</script>

<template>
    <Card>
        <div class="p-6">
            <h3 class="text-lg font-semibold">Invoices</h3>
        </div>

        <div
            v-if="invoices.data.length === 0"
            class="flex flex-col items-center justify-center py-12"
        >
            <FileText class="h-12 w-12 text-muted-foreground/50" />
            <p class="mt-4 text-sm text-muted-foreground">
                No invoices found for this client
            </p>
        </div>

        <div
            v-else
            class="border-sidebar-border/70 dark:border-sidebar-border overflow-hidden rounded-lg border-t"
        >
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead
                        class="bg-muted/50 text-left text-xs font-medium tracking-wider text-muted-foreground uppercase"
                    >
                        <tr>
                            <th class="px-6 py-3">Invoice #</th>
                            <th class="px-6 py-3">Issue Date</th>
                            <th class="px-6 py-3">Due Date</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-right">Amount</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody
                        class="divide-sidebar-border/70 dark:divide-sidebar-border divide-y bg-card"
                    >
                        <tr
                            v-for="invoice in invoices.data"
                            :key="invoice.id"
                            class="transition-colors hover:bg-muted/50"
                        >
                            <td
                                class="px-6 py-4 text-sm font-medium whitespace-nowrap"
                            >
                                {{ invoice.number }}
                            </td>
                            <td
                                class="px-6 py-4 text-sm whitespace-nowrap text-muted-foreground"
                            >
                                {{ formatDate(invoice.issue_date) }}
                            </td>
                            <td
                                class="px-6 py-4 text-sm whitespace-nowrap text-muted-foreground"
                            >
                                {{ formatDate(invoice.due_date) }}
                            </td>
                            <td class="px-6 py-4 text-sm whitespace-nowrap">
                                <Badge
                                    :variant="
                                        getStatusConfig(invoice.status).variant
                                    "
                                    :class="
                                        getStatusConfig(invoice.status).class
                                    "
                                >
                                    {{
                                        getStatusConfig(invoice.status).label
                                    }}
                                </Badge>
                            </td>
                            <td
                                class="px-6 py-4 text-right text-sm font-medium whitespace-nowrap"
                            >
                                {{ formatBRL(invoice.total) }}
                            </td>
                            <td
                                class="px-6 py-4 text-right text-sm whitespace-nowrap"
                            >
                                <Button variant="ghost" size="sm" as-child>
                                    <Link
                                        :href="
                                            showInvoice({
                                                invoice: invoice.id,
                                            }).url
                                        "
                                    >
                                        <Eye class="mr-2 h-4 w-4" />
                                        View
                                    </Link>
                                </Button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div
                v-if="invoices.last_page > 1"
                class="border-sidebar-border/70 dark:border-sidebar-border flex items-center justify-between border-t px-6 py-3"
            >
                <div class="text-sm text-muted-foreground">
                    Showing
                    {{
                        (invoices.current_page - 1) * invoices.per_page + 1
                    }}
                    to
                    {{
                        Math.min(
                            invoices.current_page * invoices.per_page,
                            invoices.total,
                        )
                    }}
                    of {{ invoices.total }} invoices
                </div>
                <div class="flex gap-2">
                    <Button
                        v-for="link in invoices.links"
                        :key="link.label"
                        :variant="link.active ? 'default' : 'ghost'"
                        size="sm"
                        :disabled="!link.url"
                        as-child
                    >
                        <Link v-if="link.url" :href="link.url">
                            <span v-html="link.label" />
                        </Link>

                        <span v-else v-html="link.label" />
                    </Button>
                </div>
            </div>
        </div>
    </Card>
</template>
