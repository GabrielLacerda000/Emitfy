<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Download, Pencil, Plus, Trash2, X } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import ExportController from '@/actions/App/Http/Controllers/ExportController';
import InvoiceController from '@/actions/App/Http/Controllers/InvoiceController';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
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
import AppLayout from '@/layouts/AppLayout.vue';
import { formatBRL, formatDate, isOverdue } from '@/lib/utils';
import { create, edit, index, show } from '@/routes/invoices';
import { type BreadcrumbItem, type Invoice, type InvoiceStatus } from '@/types';

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
    {
        title: 'Invoices',
        href: index().url,
    },
];

const statusFilters = [
    { value: null, label: 'All' },
    { value: 'draft', label: 'Draft' },
    { value: 'sent', label: 'Sent' },
    { value: 'paid', label: 'Paid' },
    { value: 'overdue', label: 'Overdue' },
] as const;

const activeStatus = computed(() => props.filters?.status || null);

function getStatusConfig(status: InvoiceStatus) {
    const configs = {
        draft: {
            variant: 'secondary' as const,
            label: 'Draft',
            class: '',
        },
        sent: {
            variant: 'default' as const,
            label: 'Sent',
            class: '',
        },
        paid: {
            variant: 'outline' as const,
            label: 'Paid',
            class: 'border-green-600 text-green-700 dark:border-green-500 dark:text-green-400',
        },
        overdue: {
            variant: 'destructive' as const,
            label: 'Overdue',
            class: '',
        },
    };
    return configs[status];
}

const deleteDialogOpen = ref(false);
const invoiceToDelete = ref<Invoice | null>(null);
const deleting = ref(false);

// Date filters
const dateFrom = ref(props.filters?.date_from ?? '');
const dateTo = ref(props.filters?.date_to ?? '');

// Export state
const exportingCsv = ref(false);

function changeStatusFilter(status: InvoiceStatus | null) {
    const params: Record<string, string> = {};

    if (status !== null) {
        params.status = status;
    }

    if (dateFrom.value) {
        params.date_from = dateFrom.value;
    }

    if (dateTo.value) {
        params.date_to = dateTo.value;
    }

    router.get(index().url, params, { preserveScroll: true });
}

function applyFilters() {
    const params: Record<string, string> = {};

    if (activeStatus.value) {
        params.status = activeStatus.value;
    }

    if (dateFrom.value) {
        console.log(dateFrom.value);
        params.date_from = dateFrom.value;
        console.log(params.date_from);
    }

    if (dateTo.value) {
        params.date_to = dateTo.value;
    }

    router.get(index().url, params, { preserveScroll: true });
}

function clearDateFilters() {
    dateFrom.value = '';
    dateTo.value = '';
    applyFilters();
}

function exportCsv() {
    exportingCsv.value = true;

    // Build URL with current filters
    const params = new URLSearchParams();
    if (activeStatus.value) params.append('status', activeStatus.value);
    if (dateFrom.value) params.append('date_from', dateFrom.value);
    if (dateTo.value) params.append('date_to', dateTo.value);

    const url = ExportController.invoicesCsv.url();
    const fullUrl = params.toString() ? `${url}?${params}` : url;
    window.location.href = fullUrl;

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

watch([dateFrom, dateTo], () => {
    applyFilters();
});
</script>

<template>
    <Head title="Invoices" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <Heading
                    title="Invoices"
                    description="Manage your invoices and track payments"
                />
                <div class="flex items-center gap-2">
                    <Button
                        variant="outline"
                        :disabled="exportingCsv"
                        @click="exportCsv"
                    >
                        <Download class="mr-2 h-4 w-4" />
                        {{ exportingCsv ? 'Exporting...' : 'Export CSV' }}
                    </Button>
                    <Button as-child>
                        <Link :href="create().url">
                            <Plus class="mr-2 h-4 w-4" />
                            New Invoice
                        </Link>
                    </Button>
                </div>
            </div>

            <!-- Status Filter Bar -->
            <div
                class="inline-flex gap-1 rounded-lg bg-muted p-1 dark:bg-muted/50"
            >
                <button
                    v-for="filter in statusFilters"
                    :key="filter.label"
                    @click="changeStatusFilter(filter.value)"
                    :class="[
                        'flex items-center rounded-md px-3.5 py-1.5 transition-all duration-200',
                        activeStatus === filter.value
                            ? 'bg-background text-foreground shadow-sm'
                            : 'text-muted-foreground hover:bg-background/50 hover:text-foreground',
                    ]"
                >
                    <span class="text-sm font-medium">{{ filter.label }}</span>
                </button>
            </div>

            <!-- Date Range Filter -->
            <Card class="p-4">
                <div class="flex items-end gap-4">
                    <div class="flex-1">
                        <label
                            for="date-from"
                            class="mb-1.5 block text-sm font-medium"
                        >
                            From Date
                        </label>
                        <Input id="date-from" v-model="dateFrom" type="date" />
                    </div>
                    <div class="flex-1">
                        <label
                            for="date-to"
                            class="mb-1.5 block text-sm font-medium"
                        >
                            To Date
                        </label>
                        <Input id="date-to" v-model="dateTo" type="date" />
                    </div>
                    <Button
                        variant="outline"
                        :disabled="!dateFrom && !dateTo"
                        @click="clearDateFilters"
                    >
                        <X class="mr-2 h-4 w-4" />
                        Clear Dates
                    </Button>
                </div>
            </Card>

            <!-- Empty State -->
            <div
                v-if="props.invoices.data.length === 0"
                class="border-sidebar-border/70 dark:border-sidebar-border flex flex-1 flex-col items-center justify-center rounded-xl border border-dashed p-8"
            >
                <div class="text-center">
                    <h3 class="text-lg font-medium">No invoices yet</h3>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Get started by creating your first invoice.
                    </p>
                    <Button class="mt-4" as-child>
                        <Link :href="create().url">
                            <Plus class="mr-2 h-4 w-4" />
                            New Invoice
                        </Link>
                    </Button>
                </div>
            </div>

            <!-- Invoices Table -->
            <div
                v-else
                class="border-sidebar-border/70 dark:border-sidebar-border rounded-xl border"
            >
                <table class="w-full">
                    <thead>
                        <tr
                            class="border-sidebar-border/70 dark:border-sidebar-border border-b"
                        >
                            <th
                                class="px-4 py-3 text-left text-sm font-medium text-muted-foreground"
                            >
                                Number
                            </th>
                            <th
                                class="px-4 py-3 text-left text-sm font-medium text-muted-foreground"
                            >
                                Client
                            </th>
                            <th
                                class="px-4 py-3 text-left text-sm font-medium text-muted-foreground"
                            >
                                Amount
                            </th>
                            <th
                                class="px-4 py-3 text-left text-sm font-medium text-muted-foreground"
                            >
                                Status
                            </th>
                            <th
                                class="px-4 py-3 text-left text-sm font-medium text-muted-foreground"
                            >
                                Due Date
                            </th>
                            <th
                                class="px-4 py-3 text-right text-sm font-medium text-muted-foreground"
                            >
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="invoice in props.invoices.data"
                            :key="invoice.id"
                            class="border-sidebar-border/70 dark:border-sidebar-border border-b last:border-b-0"
                        >
                            <td class="px-4 py-3 text-sm">
                                <Link
                                    :href="show({ invoice: invoice.id }).url"
                                    class="font-medium text-foreground hover:underline"
                                >
                                    {{ invoice.number }}
                                </Link>
                            </td>
                            <td class="px-4 py-3 text-sm text-muted-foreground">
                                {{ invoice.client.name }}
                                <span
                                    v-if="invoice.client.company_name"
                                    class="text-xs"
                                >
                                    ({{ invoice.client.company_name }})
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm font-medium">
                                {{ formatBRL(invoice.total) }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <Badge
                                    :variant="
                                        getStatusConfig(invoice.status).variant
                                    "
                                    :class="
                                        getStatusConfig(invoice.status).class
                                    "
                                >
                                    {{ getStatusConfig(invoice.status).label }}
                                </Badge>
                            </td>
                            <td
                                class="px-4 py-3 text-sm"
                                :class="{
                                    'text-destructive':
                                        isOverdue(
                                            invoice.due_date,
                                            invoice.paid_at,
                                        ) && invoice.status !== 'paid',
                                }"
                            >
                                {{ formatDate(invoice.due_date) }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div
                                    class="flex items-center justify-end gap-2"
                                >
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        as-child
                                    >
                                        <Link
                                            :href="
                                                edit({ invoice: invoice.id })
                                                    .url
                                            "
                                        >
                                            <Pencil class="h-4 w-4" />
                                            <span class="sr-only">Edit</span>
                                        </Link>
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        @click="confirmDelete(invoice)"
                                        class="cursor-pointer"
                                    >
                                        <Trash2
                                            class="h-4 w-4 text-destructive"
                                        />
                                        <span class="sr-only">Delete</span>
                                    </Button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div
                    v-if="props.invoices.last_page > 1"
                    class="border-sidebar-border/70 dark:border-sidebar-border flex items-center justify-between border-t px-4 py-3"
                >
                    <p class="text-sm text-muted-foreground">
                        Page {{ props.invoices.current_page }} of
                        {{ props.invoices.last_page }} ({{
                            props.invoices.total
                        }}
                        total)
                    </p>
                    <div class="flex gap-1">
                        <template
                            v-for="link in props.invoices.links"
                            :key="link.label"
                        >
                            <Button
                                v-if="link.url"
                                variant="outline"
                                size="sm"
                                :class="{ 'bg-accent': link.active }"
                                as-child
                            >
                                <Link :href="link.url">
                                    <span v-html="link.label" />
                                </Link>
                            </Button>

                            <Button v-else variant="outline" size="sm" disabled>
                                <span v-html="link.label" />
                            </Button>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Dialog -->
        <Dialog v-model:open="deleteDialogOpen">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Delete Invoice</DialogTitle>
                    <DialogDescription>
                        Are you sure you want to delete invoice
                        <strong>{{ invoiceToDelete?.number }}</strong
                        >? This action cannot be undone.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter class="gap-2">
                    <DialogClose as-child>
                        <Button variant="secondary">Cancel</Button>
                    </DialogClose>
                    <Button
                        variant="destructive"
                        :disabled="deleting"
                        @click="deleteInvoice"
                    >
                        Delete
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
