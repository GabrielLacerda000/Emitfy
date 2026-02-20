<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowRight, FileText, Plus, Calendar, User2, ExternalLink } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { useInvoiceStatus } from '@/composables/useInvoiceStatus';
import { useFormatCurrency } from '@/composables/useLocale';
import { formatDate } from '@/lib/utils';
import type { Invoice } from '@/types';

const { t } = useI18n();
const formatMoney = useFormatCurrency();
const { getStatusConfig } = useInvoiceStatus();

type Props = {
    invoices: Invoice[];
    viewAllUrl: string;
    createInvoiceUrl: string;
};

defineProps<Props>();
</script>

<template>
    <div class="flex flex-col gap-5">
        <div class="flex items-center justify-between px-1">
            <div>
                <h3 class="text-lg font-bold tracking-tight">{{ t('dashboard.recentInvoices.title') }}</h3>
                <p class="text-xs text-muted-foreground font-medium">{{ t('dashboard.recentInvoices.subtitle') }}</p>
            </div>
            <Button variant="outline" size="sm" class="h-9 rounded-lg border-border/60 hover:bg-primary/5 hover:text-primary transition-all" as-child>
                <Link :href="viewAllUrl">
                    {{ t('dashboard.recentInvoices.viewAll') }}
                    <ArrowRight class="ml-2 h-3.5 w-3.5" />
                </Link>
            </Button>
        </div>

        <div v-if="invoices.length > 0" class="relative overflow-hidden rounded-2xl border border-border/50 bg-card shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-border/50 bg-muted/20">
                            <th class="h-11 px-4 text-left align-middle text-xs font-bold uppercase tracking-wider text-muted-foreground">
                                {{ t('dashboard.recentInvoices.headers.invoice') }}
                            </th>
                            <th class="h-11 px-4 text-left align-middle text-xs font-bold uppercase tracking-wider text-muted-foreground">
                                {{ t('dashboard.recentInvoices.headers.client') }}
                            </th>
                            <th class="h-11 px-4 text-left align-middle text-xs font-bold uppercase tracking-wider text-muted-foreground">
                                {{ t('dashboard.recentInvoices.headers.status') }}
                            </th>
                            <th class="h-11 px-4 text-right align-middle text-xs font-bold uppercase tracking-wider text-muted-foreground">
                                {{ t('dashboard.recentInvoices.headers.amount') }}
                            </th>
                            <th class="h-11 w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/40">
                        <tr
                            v-for="invoice in invoices"
                            :key="invoice.id"
                            class="group transition-colors hover:bg-muted/30"
                        >
                            <td class="p-4 align-middle">
                                <div class="flex flex-col gap-1">
                                    <span class="font-bold text-foreground group-hover:text-primary transition-colors">
                                        {{ invoice.number }}
                                    </span>
                                    <div class="flex items-center gap-1.5 text-xs text-muted-foreground">
                                        <Calendar class="h-3 w-3" />
                                        {{ t('dashboard.recentInvoices.due') }} {{ formatDate(invoice.due_date) }}
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 align-middle">
                                <div class="flex items-center gap-2.5">
                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-muted/60 text-muted-foreground group-hover:bg-primary/10 group-hover:text-primary transition-colors">
                                        <User2 class="h-4 w-4" />
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-semibold text-foreground leading-none">
                                            {{ invoice.client.name }}
                                        </span>
                                        <span class="text-[11px] text-muted-foreground mt-1 truncate max-w-[150px]">
                                            {{ invoice.client.company_name || invoice.client.email }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 align-middle">
                                <Badge
                                    :variant="getStatusConfig(invoice.status).variant"
                                    :class="[
                                        getStatusConfig(invoice.status).class,
                                        'rounded-md px-2 py-0.5 font-bold text-[10px] uppercase tracking-wider'
                                    ]"
                                >
                                    {{ getStatusConfig(invoice.status).label }}
                                </Badge>
                            </td>
                            <td class="p-4 align-middle text-right">
                                <span class="font-black text-foreground tracking-tight">
                                    {{ formatMoney(invoice.total) }}
                                </span>
                            </td>
                            <td class="p-4 align-middle text-right">
                                <Button size="icon" variant="ghost" class="h-8 w-8 opacity-0 group-hover:opacity-100 transition-opacity" as-child>
                                    <Link :href="`/invoices/${invoice.id}`"> <ExternalLink class="h-4 w-4" />
                                    </Link>
                                </Button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div
            v-else
            class="group flex flex-col items-center justify-center rounded-4xl border-2 border-dashed border-border/60 p-12 text-center transition-all hover:border-primary/20 bg-muted/5 group"
        >
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-background shadow-sm border border-border group-hover:scale-110 transition-transform duration-500">
                <FileText class="h-8 w-8 text-muted-foreground/60 group-hover:text-primary/60 transition-colors" />
            </div>
            <h3 class="mt-6 text-lg font-bold">{{ t('dashboard.recentInvoices.noInvoices') }}</h3>
            <p class="mt-2 text-sm text-muted-foreground max-w-[220px]">
                {{ t('dashboard.recentInvoices.noInvoicesDesc') }}
            </p>
            <Button class="mt-8 rounded-full px-8 shadow-lg shadow-primary/20 hover:shadow-primary/30" as-child>
                <Link :href="createInvoiceUrl">
                    <Plus class="mr-2 h-4 w-4" />
                    {{ t('dashboard.recentInvoices.newInvoice') }}
                </Link>
            </Button>
        </div>
    </div>
</template>
