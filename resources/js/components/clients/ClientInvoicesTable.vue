<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    Eye,
    FileText,
    ReceiptText,
    ChevronLeft,
    ChevronRight,
} from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { useInvoiceStatus } from '@/composables/useInvoiceStatus';
import { useFormatCurrency } from '@/composables/useLocale';
import { formatDate } from '@/lib/utils';
import { show as showInvoice } from '@/routes/invoices';
import type { PaginatedInvoices } from '@/types';

const { t } = useI18n();
const formatMoney = useFormatCurrency();
const { getStatusConfig } = useInvoiceStatus();

interface Props {
    invoices: PaginatedInvoices;
}

defineProps<Props>();
</script>

<template>
    <Card
        class="overflow-hidden rounded-3xl border-none bg-background shadow-sm"
    >
        <div class="flex items-center justify-between p-6">
            <div class="flex items-center gap-2">
                <div class="rounded-xl bg-primary/10 p-2 text-primary">
                    <ReceiptText class="h-5 w-5" />
                </div>
                <div>
                    <h3 class="text-lg font-bold tracking-tight">{{ t('clients.invoices.title') }}</h3>
                    <p class="text-xs text-muted-foreground">
                        {{ t('clients.invoices.subtitle') }}
                    </p>
                </div>
            </div>

            <Badge variant="outline" class="rounded-full font-bold">
                {{ invoices.total }} total
            </Badge>
        </div>

        <div
            v-if="invoices.data.length === 0"
            class="flex flex-col items-center justify-center border-t border-border/50 py-20"
        >
            <div class="relative">
                <FileText class="h-16 w-16 text-muted-foreground/20" />
                <div
                    class="absolute -right-1 -bottom-1 rounded-full bg-background p-1"
                >
                    <div class="h-3 w-3 rounded-full bg-muted-foreground/30" />
                </div>
            </div>
            <p
                class="mt-4 text-sm font-bold tracking-widest text-muted-foreground/70 uppercase"
            >
                {{ t('clients.invoices.noInvoices') }}
            </p>
            <p class="text-xs text-muted-foreground">
                {{ t('clients.invoices.noInvoicesDesc') }}
            </p>
        </div>

        <div v-else class="border-t border-border/50">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead class="text-left">
                        <tr
                            class="border-b border-border/40 text-[10px] font-black tracking-[0.15em] text-muted-foreground/60 uppercase"
                        >
                            <th class="px-6 py-4">{{ t('clients.invoices.headers.invoice') }}</th>
                            <th class="px-6 py-4">{{ t('clients.invoices.headers.status') }}</th>
                            <th class="px-6 py-4">{{ t('clients.invoices.headers.dates') }}</th>
                            <th class="px-6 py-4 text-right">{{ t('clients.invoices.headers.amount') }}</th>
                            <th class="px-6 py-4 text-right italic">{{ t('clients.invoices.headers.action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/30">
                        <tr
                            v-for="invoice in invoices.data"
                            :key="invoice.id"
                            class="group transition-colors hover:bg-muted/30"
                        >
                            <td class="px-6 py-4">
                                <span
                                    class="text-sm font-bold text-foreground transition-colors group-hover:text-primary"
                                >
                                    #{{ invoice.number }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <Badge
                                    :variant="
                                        getStatusConfig(invoice.status).variant
                                    "
                                    :class="[
                                        'rounded-full px-2.5 py-0.5 text-[10px] font-black tracking-tight uppercase',
                                        getStatusConfig(invoice.status).class,
                                    ]"
                                >
                                    {{ getStatusConfig(invoice.status).label }}
                                </Badge>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span
                                        class="text-sm font-bold text-foreground"
                                    >
                                        {{ formatDate(invoice.issue_date) }}
                                    </span>
                                    <span
                                        class="flex items-center gap-1 text-[10px] text-muted-foreground"
                                    >
                                        {{ t('clients.invoices.dueOn') }}
                                        {{ formatDate(invoice.due_date) }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-right">
                                <span
                                    class="text-sm font-black text-foreground"
                                >
                                    {{ formatMoney(invoice.total) }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right">
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    class="rounded-full transition-all hover:bg-primary hover:text-primary-foreground"
                                    as-child
                                >
                                    <Link
                                        :href="
                                            showInvoice({ invoice: invoice.id })
                                                .url
                                        "
                                    >
                                        <Eye class="h-4 w-4" />
                                    </Link>
                                </Button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div
                v-if="invoices.last_page > 1"
                class="flex items-center justify-between border-t border-border/50 bg-muted/20 px-6 py-4"
            >
                <p
                    class="text-[11px] font-bold tracking-wider text-muted-foreground uppercase"
                >
                    {{ t('clients.invoices.pagination.info', { total: invoices.total, current: invoices.current_page, last: invoices.last_page }) }}
                </p>

                <div class="flex gap-1">
                    <Button
                        v-for="link in invoices.links"
                        :key="link.label"
                        :variant="link.active ? 'default' : 'ghost'"
                        class="h-8 w-8 rounded-lg p-0 text-xs font-bold transition-all"
                        :disabled="!link.url"
                        as-child
                    >
                        <Link v-if="link.url" :href="link.url">
                            <template v-if="link.label.includes('Previous')"
                                ><ChevronLeft class="h-4 w-4"
                            /></template>
                            <template v-else-if="link.label.includes('Next')"
                                ><ChevronRight class="h-4 w-4"
                            /></template>
                            <template v-else>{{ link.label }}</template>
                        </Link>
                        <span v-else>
                            <template v-if="link.label.includes('Previous')"
                                ><ChevronLeft class="h-4 w-4"
                            /></template>
                            <template v-else-if="link.label.includes('Next')"
                                ><ChevronRight class="h-4 w-4"
                            /></template>
                            <template v-else>{{ link.label }}</template>
                        </span>
                    </Button>
                </div>
            </div>
        </div>
    </Card>
</template>
