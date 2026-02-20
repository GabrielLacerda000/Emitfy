<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Download,
    Pencil,
    Plus,
    Trash2,
    X,
    Calendar as CalendarIcon,
    FileText,
    ArrowUpRight,
} from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import ExportController from '@/actions/App/Http/Controllers/ExportController';
import InvoiceController from '@/actions/App/Http/Controllers/InvoiceController';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { useFormatCurrency } from '@/composables/useLocale';
import AppLayout from '@/layouts/AppLayout.vue';
import { formatDate, isOverdue } from '@/lib/utils';
import { create, edit, index, show } from '@/routes/invoices';
import { type BreadcrumbItem, type Invoice, type InvoiceStatus } from '@/types';

const { t } = useI18n();
const formatMoney = useFormatCurrency();

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}
interface PaginatedInvoices {
    data: Invoice[];
    links: PaginationLink[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}
type Props = {
    invoices: PaginatedInvoices;
    status?: InvoiceStatus | null;
    filters?: {
        status?: InvoiceStatus | null;
        date_from?: string;
        date_to?: string;
    };
};
const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: t('nav.invoices'), href: index().url },
];

const statusFilters = computed(() => [
    { value: null, label: t('invoices.filters.all') },
    { value: 'draft', label: t('invoices.filters.draft') },
    { value: 'sent', label: t('invoices.filters.sent') },
    { value: 'paid', label: t('invoices.filters.paid') },
    { value: 'overdue', label: t('invoices.filters.overdue') },
] as const);

const activeStatus = computed(() => props.filters?.status || null);

function getStatusConfig(status: InvoiceStatus) {
    const configs = {
        draft: {
            variant: 'secondary' as const,
            label: t('invoices.status.draft'),
            class: 'bg-slate-100 text-slate-600 border-slate-200 dark:bg-slate-900/40 dark:text-slate-400 dark:border-slate-800',
        },
        sent: {
            variant: 'default' as const,
            label: t('invoices.status.sent'),
            class: 'bg-blue-50 text-blue-600 border-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800',
        },
        paid: {
            variant: 'outline' as const,
            label: t('invoices.status.paid'),
            class: 'bg-emerald-50 text-emerald-600 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-800',
        },
        overdue: {
            variant: 'destructive' as const,
            label: t('invoices.status.overdue'),
            class: 'bg-rose-50 text-rose-600 border-rose-200 dark:bg-rose-900/30 dark:text-rose-400 dark:border-rose-800',
        },
    };
    return configs[status];
}

const deleteDialogOpen = ref(false);
const invoiceToDelete = ref<Invoice | null>(null);
const deleting = ref(false);
const dateFrom = ref(props.filters?.date_from ?? '');
const dateTo = ref(props.filters?.date_to ?? '');
const exportingCsv = ref(false);

function changeStatusFilter(status: InvoiceStatus | null) {
    const params: Record<string, string> = {};
    if (status !== null) params.status = status;
    if (dateFrom.value) params.date_from = dateFrom.value;
    if (dateTo.value) params.date_to = dateTo.value;
    router.get(index().url, params, { preserveScroll: true });
}

function applyFilters() {
    const params: Record<string, string> = {};
    if (activeStatus.value) params.status = activeStatus.value;
    if (dateFrom.value) params.date_from = dateFrom.value;
    if (dateTo.value) params.date_to = dateTo.value;
    router.get(index().url, params, { preserveScroll: true });
}

function clearDateFilters() {
    dateFrom.value = '';
    dateTo.value = '';
    applyFilters();
}

function exportCsv() {
    exportingCsv.value = true;
    const params = new URLSearchParams();
    if (activeStatus.value) params.append('status', activeStatus.value);
    if (dateFrom.value) params.append('date_from', dateFrom.value);
    if (dateTo.value) params.append('date_to', dateTo.value);
    const url = ExportController.invoicesCsv.url();
    window.location.href = params.toString() ? `${url}?${params}` : url;
    setTimeout(() => (exportingCsv.value = false), 2000);
}

function confirmDelete(invoice: Invoice) {
    invoiceToDelete.value = invoice;
    deleteDialogOpen.value = true;
}

function deleteInvoice() {
    if (!invoiceToDelete.value) return;
    deleting.value = true;
    router.delete(
        InvoiceController.destroy.url({ invoice: invoiceToDelete.value.id }),
        {
            preserveScroll: true,
            onFinish: () => {
                deleting.value = false;
                deleteDialogOpen.value = false;
                invoiceToDelete.value = null;
            },
        },
    );
}

watch([dateFrom, dateTo], () => applyFilters());

const thClass =
    'px-6 py-4 text-left text-[10px] font-black tracking-[0.2em] text-muted-foreground/80 uppercase';
</script>

<template>
    <Head title="Invoices" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 bg-muted/5 p-6">
            <div
                class="flex flex-col justify-between gap-4 md:flex-row md:items-end"
            >
                <Heading
                    :title="t('invoices.title')"
                    :description="t('invoices.description')"
                />
                <div class="flex items-center gap-3">
                    <Button
                        variant="outline"
                        :disabled="exportingCsv"
                        @click="exportCsv"
                        class="h-11 rounded-xl border-border/60 font-bold shadow-sm hover:bg-background"
                    >
                        <Download class="mr-2 h-4 w-4" />
                        {{ exportingCsv ? t('invoices.exporting') : t('invoices.csv') }}
                    </Button>
                    <Button
                        as-child
                        class="h-11 rounded-xl bg-primary px-6 shadow-lg shadow-primary/20 transition-all hover:-translate-y-0.5 hover:shadow-primary/30"
                    >
                        <Link :href="create().url">
                            <Plus class="mr-2 h-4 w-4" />
                            {{ t('invoices.newInvoice') }}
                        </Link>
                    </Button>
                </div>
            </div>

            <div class="grid grid-cols-1 items-center gap-4 lg:grid-cols-12">
                <div
                    class="flex w-fit flex-wrap gap-1 rounded-2xl border border-border/40 bg-muted/40 p-1.5 lg:col-span-7"
                >
                    <button
                        v-for="filter in statusFilters"
                        :key="filter.label"
                        @click="changeStatusFilter(filter.value)"
                        :class="[
                            'flex items-center rounded-xl px-4 py-2 text-xs font-bold transition-all duration-200',
                            activeStatus === filter.value
                                ? 'bg-background text-primary shadow-sm ring-1 ring-border/50'
                                : 'text-muted-foreground hover:text-foreground',
                        ]"
                    >
                        {{ filter.label }}
                    </button>
                </div>

                <div class="flex items-center gap-2 lg:col-span-5">
                    <div class="relative flex-1">
                        <CalendarIcon
                            class="absolute top-1/2 left-3 h-3.5 w-3.5 -translate-y-1/2 text-muted-foreground/50"
                        />
                        <Input
                            v-model="dateFrom"
                            type="date"
                            class="h-10 rounded-xl border-border/40 bg-background/50 pl-9 transition-all focus:bg-background"
                        />
                    </div>
                    <div class="relative flex-1">
                        <CalendarIcon
                            class="absolute top-1/2 left-3 h-3.5 w-3.5 -translate-y-1/2 text-muted-foreground/50"
                        />
                        <Input
                            v-model="dateTo"
                            type="date"
                            class="h-10 rounded-xl border-border/40 bg-background/50 pl-9 transition-all focus:bg-background"
                        />
                    </div>
                    <Button
                        v-if="dateFrom || dateTo"
                        variant="ghost"
                        size="icon"
                        @click="clearDateFilters"
                        class="h-10 w-10 rounded-xl text-muted-foreground transition-colors hover:text-destructive"
                    >
                        <X class="h-4 w-4" />
                    </Button>
                </div>
            </div>

            <div
                v-if="props.invoices.data.length === 0"
                class="flex flex-1 flex-col items-center justify-center rounded-3xl border border-dashed border-border/60 bg-background/40 p-12"
            >
                <div
                    class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-muted/50"
                >
                    <FileText class="h-8 w-8 text-muted-foreground/30" />
                </div>
                <h3 class="text-lg font-bold">{{ t('invoices.empty.title') }}</h3>
                <p class="mt-1 mb-6 text-sm text-muted-foreground">
                    {{ t('invoices.empty.desc') }}
                </p>
                <Button variant="outline" as-child class="rounded-xl border-2">
                    <Link :href="create().url">{{ t('invoices.empty.cta') }}</Link>
                </Button>
            </div>

            <div
                v-else
                class="overflow-hidden rounded-2xl border border-border/60 bg-background shadow-sm"
            >
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-border/60 bg-muted/30">
                                <th :class="thClass">{{ t('invoices.table.invoice') }}</th>
                                <th :class="thClass">{{ t('invoices.table.client') }}</th>
                                <th :class="thClass">{{ t('invoices.table.amount') }}</th>
                                <th :class="thClass">{{ t('invoices.table.status') }}</th>
                                <th :class="thClass">{{ t('invoices.table.dueDate') }}</th>
                                <th
                                    class="px-6 py-4 text-right text-[10px] font-black tracking-[0.2em] text-muted-foreground/80 uppercase"
                                >
                                    {{ t('invoices.table.actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border/40">
                            <tr
                                v-for="invoice in props.invoices.data"
                                :key="invoice.id"
                                class="group cursor-pointer transition-colors hover:bg-muted/10"
                                @click="
                                    router.visit(
                                        show({ invoice: invoice.id }).url,
                                    )
                                "
                            >
                                <td class="px-6 py-4">
                                    <span
                                        class="flex items-center gap-1 text-sm font-black tracking-tight text-primary"
                                    >
                                        {{ invoice.number }}
                                        <ArrowUpRight
                                            class="h-3 w-3 -translate-y-0.5 opacity-0 transition-all group-hover:opacity-100"
                                        />
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-sm font-bold tracking-tight"
                                            >{{ invoice.client.name }}</span
                                        >
                                        <span
                                            v-if="invoice.client.company_name"
                                            class="text-[11px] font-medium tracking-tighter text-muted-foreground uppercase"
                                        >
                                            {{ invoice.client.company_name }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="font-mono text-sm font-bold tracking-tight"
                                    >
                                        {{ formatMoney(invoice.total) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <Badge
                                        variant="outline"
                                        :class="[
                                            'rounded-lg border px-2.5 py-0.5 text-[11px] font-bold tracking-wider uppercase shadow-none',
                                            getStatusConfig(invoice.status)
                                                .class,
                                        ]"
                                    >
                                        {{
                                            getStatusConfig(invoice.status)
                                                .label
                                        }}
                                    </Badge>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        :class="[
                                            'text-sm font-medium',
                                            isOverdue(
                                                invoice.due_date,
                                                invoice.paid_at,
                                            ) && invoice.status !== 'paid'
                                                ? 'font-bold text-rose-500'
                                                : 'text-muted-foreground',
                                        ]"
                                    >
                                        {{ formatDate(invoice.due_date) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right" @click.stop>
                                    <div
                                        class="flex items-center justify-end gap-1"
                                    >
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            class="h-8 w-8 rounded-lg shadow-none hover:bg-background hover:shadow-sm"
                                            as-child
                                        >
                                            <Link
                                                :href="
                                                    edit({
                                                        invoice: invoice.id,
                                                    }).url
                                                "
                                            >
                                                <Pencil class="h-3.5 w-3.5" />
                                            </Link>
                                        </Button>
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            class="h-8 w-8 rounded-lg text-destructive hover:bg-destructive/5"
                                            @click="confirmDelete(invoice)"
                                        >
                                            <Trash2 class="h-3.5 w-3.5" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    v-if="props.invoices.last_page > 1"
                    class="flex items-center justify-between border-t border-border/60 bg-muted/10 px-6 py-4"
                >
                    <p
                        class="text-[10px] font-black tracking-widest text-muted-foreground/60 uppercase"
                    >
                        {{ t('invoices.pagination.page', { current: props.invoices.current_page, last: props.invoices.last_page }) }}
                    </p>
                    <div class="flex gap-2">
                        <template
                            v-for="link in props.invoices.links"
                            :key="link.label"
                        >
                            <Button
                                v-if="link.url"
                                variant="ghost"
                                size="sm"
                                :class="[
                                    'h-8 rounded-lg px-3 text-xs font-bold transition-all',
                                    link.active
                                        ? 'border border-border/60 bg-background text-primary shadow-sm'
                                        : 'text-muted-foreground',
                                ]"
                                as-child
                            >
                                <Link :href="link.url"
                                    ><span v-html="link.label"
                                /></Link>
                            </Button>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <Dialog v-model:open="deleteDialogOpen">
            <DialogContent
                class="rounded-2xl border-none shadow-2xl sm:max-w-[425px]"
            >
                <DialogHeader>
                    <div
                        class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-rose-50 dark:bg-rose-900/20"
                    >
                        <Trash2 class="h-7 w-7 text-rose-500" />
                    </div>

                    <DialogTitle class="text-center text-xl font-bold">
                        {{ t('invoices.delete.title') }}
                    </DialogTitle>

                    <DialogDescription class="pt-2 text-center">
                        {{ t('invoices.delete.desc', { number: invoiceToDelete?.number }) }}
                    </DialogDescription>
                </DialogHeader>

                <DialogFooter
                    class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end"
                >
                    <DialogClose as-child>
                        <Button
                            variant="outline"
                            class="flex-1 rounded-xl border-2 font-bold"
                        >
                            {{ t('invoices.delete.cancel') }}
                        </Button>
                    </DialogClose>

                    <Button
                        variant="destructive"
                        class="flex-1 rounded-xl font-bold shadow-lg shadow-rose-500/20"
                        :disabled="deleting"
                        @click="deleteInvoice"
                    >
                        {{ deleting ? t('invoices.delete.deleting') : t('invoices.delete.confirm') }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
