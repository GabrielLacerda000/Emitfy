<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { computed, ref } from 'vue';
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
import AppLayout from '@/layouts/AppLayout.vue';
import { formatCurrency, formatDate, isOverdue } from '@/lib/utils';
import { create, edit, index, show } from '@/routes/invoices';
import {
    type BreadcrumbItem,
    type Invoice,
    type InvoiceStatus,
} from '@/types';

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

const activeStatus = computed(() => props.status ?? null);

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

function changeStatusFilter(status: InvoiceStatus | null) {
    if (status === null) {
        router.get(index().url, {}, { preserveScroll: true });
    } else {
        router.get(index().url, { status }, { preserveScroll: true });
    }
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
                <Button as-child>
                    <Link :href="create().url">
                        <Plus class="mr-2 h-4 w-4" />
                        New Invoice
                    </Link>
                </Button>
            </div>

            <!-- Status Filter Bar -->
            <div
                class="inline-flex gap-1 rounded-lg bg-neutral-100 p-1 dark:bg-neutral-800"
            >
                <button
                    v-for="filter in statusFilters"
                    :key="filter.label"
                    @click="changeStatusFilter(filter.value)"
                    :class="[
                        'flex items-center rounded-md px-3.5 py-1.5 transition-colors',
                        activeStatus === filter.value
                            ? 'bg-white shadow-xs dark:bg-neutral-700 dark:text-neutral-100'
                            : 'text-neutral-500 hover:bg-neutral-200/60 hover:text-black dark:text-neutral-400 dark:hover:bg-neutral-700/60',
                    ]"
                >
                    <span class="text-sm">{{ filter.label }}</span>
                </button>
            </div>

            <!-- Empty State -->
            <div
                v-if="props.invoices.data.length === 0"
                class="flex flex-1 flex-col items-center justify-center rounded-xl border border-dashed border-sidebar-border/70 p-8 dark:border-sidebar-border"
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
                class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
            >
                <table class="w-full">
                    <thead>
                        <tr
                            class="border-b border-sidebar-border/70 dark:border-sidebar-border"
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
                            class="border-b border-sidebar-border/70 last:border-b-0 dark:border-sidebar-border"
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
                                {{ formatCurrency(invoice.total) }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <Badge
                                    :variant="getStatusConfig(invoice.status).variant"
                                    :class="getStatusConfig(invoice.status).class"
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
                    class="flex items-center justify-between border-t border-sidebar-border/70 px-4 py-3 dark:border-sidebar-border"
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
