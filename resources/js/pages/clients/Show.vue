<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    CheckCircle,
    Clock,
    Eye,
    FileText,
    Pencil,
    Trash2,
    TrendingUp,
} from 'lucide-vue-next';
import { ref } from 'vue';
import ClientController from '@/actions/App/Http/Controllers/ClientController';
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
import { getStatusConfig } from '@/composables/useInvoiceStatus';
import AppLayout from '@/layouts/AppLayout.vue';
import { formatBRL, formatDate } from '@/lib/utils';
import { edit, index } from '@/routes/clients';
import { show as showInvoice } from '@/routes/invoices';
import type {
    BreadcrumbItem,
    Client,
    ClientStats,
    PaginatedInvoices,
} from '@/types';

type Props = {
    client: Client;
    stats: ClientStats;
    invoices: PaginatedInvoices;
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Clients',
        href: index().url,
    },
    {
        title: props.client.name,
        href: '#',
    },
];

const deleteDialogOpen = ref(false);
const deleting = ref(false);

function confirmDelete() {
    deleteDialogOpen.value = true;
}

function deleteClient() {
    deleting.value = true;
    router.delete(ClientController.destroy.url({ client: props.client.id }), {
        onFinish: () => {
            deleting.value = false;
        },
    });
}
</script>

<template>
    <Head :title="client.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="mx-auto w-full max-w-6xl">
                <!-- Header -->
                <div class="flex items-start justify-between">
                    <div>
                        <h1 class="text-2xl font-bold">{{ client.name }}</h1>
                        <p class="mt-1 text-sm text-muted-foreground">
                            {{ client.email }}
                        </p>
                    </div>

                    <div class="flex gap-2">
                        <Button variant="outline" as-child>
                            <Link :href="edit({ client: client.id }).url">
                                <Pencil class="mr-2 h-4 w-4" />
                                Edit
                            </Link>
                        </Button>
                        <Button variant="destructive" @click="confirmDelete">
                            <Trash2 class="mr-2 h-4 w-4" />
                            Delete
                        </Button>
                    </div>
                </div>

                <!-- Client Info Card -->
                <Card class="mt-6 p-6">
                    <h3 class="mb-4 text-lg font-semibold">Client Details</h3>
                    <dl class="grid gap-4 text-sm md:grid-cols-2">
                        <div>
                            <dt class="font-medium text-muted-foreground">
                                Name
                            </dt>
                            <dd class="mt-1">{{ client.name }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-muted-foreground">
                                Email
                            </dt>
                            <dd class="mt-1">{{ client.email }}</dd>
                        </div>
                        <div v-if="client.company_name">
                            <dt class="font-medium text-muted-foreground">
                                Company
                            </dt>
                            <dd class="mt-1">{{ client.company_name }}</dd>
                        </div>
                        <div v-if="stats.lastInvoiceSent">
                            <dt class="font-medium text-muted-foreground">
                                Last Invoice Sent
                            </dt>
                            <dd class="mt-1">
                                {{ formatDate(stats.lastInvoiceSent.sent_at!) }}
                            </dd>
                        </div>
                        <div v-if="client.notes" class="md:col-span-2">
                            <dt class="font-medium text-muted-foreground">
                                Notes
                            </dt>
                            <dd class="mt-1 whitespace-pre-wrap">
                                {{ client.notes }}
                            </dd>
                        </div>
                    </dl>
                </Card>

                <!-- Stats Cards -->
                <div class="mt-6 grid gap-4 md:grid-cols-4">
                    <!-- Total Paid -->
                    <Card class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p
                                    class="text-sm font-medium text-muted-foreground"
                                >
                                    Total Paid
                                </p>
                                <p class="mt-2 text-2xl font-bold">
                                    {{ formatBRL(stats.totalPaid) }}
                                </p>
                                <p class="mt-1 text-xs text-muted-foreground">
                                    {{ stats.totalPaidCount }}
                                    {{
                                        stats.totalPaidCount === 1
                                            ? 'invoice'
                                            : 'invoices'
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
                                <p
                                    class="text-sm font-medium text-muted-foreground"
                                >
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
                                <Clock
                                    class="h-6 w-6 text-blue-600 dark:text-blue-500"
                                />
                            </div>
                        </div>
                    </Card>

                    <!-- Total Overdue -->
                    <Card class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p
                                    class="text-sm font-medium text-muted-foreground"
                                >
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
                                <p
                                    class="text-sm font-medium text-muted-foreground"
                                >
                                    Total Draft
                                </p>
                                <p class="mt-2 text-2xl font-bold">
                                    {{ formatBRL(stats.totalDraft) }}
                                </p>
                                <p class="mt-1 text-xs text-muted-foreground">
                                    {{ stats.totalDraftCount }}
                                    {{
                                        stats.totalDraftCount === 1
                                            ? 'invoice'
                                            : 'invoices'
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

                <!-- Invoices Table -->
                <Card class="mt-6">
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
                                        <th class="px-6 py-3 text-right">
                                            Amount
                                        </th>
                                        <th class="px-6 py-3 text-right">
                                            Actions
                                        </th>
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
                                        <td
                                            class="px-6 py-4 text-sm whitespace-nowrap"
                                        >
                                            <Badge
                                                :variant="
                                                    getStatusConfig(
                                                        invoice.status,
                                                    ).variant
                                                "
                                                :class="
                                                    getStatusConfig(
                                                        invoice.status,
                                                    ).class
                                                "
                                            >
                                                {{
                                                    getStatusConfig(
                                                        invoice.status,
                                                    ).label
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
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                as-child
                                            >
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
                                    (invoices.current_page - 1) *
                                        invoices.per_page +
                                    1
                                }}
                                to
                                {{
                                    Math.min(
                                        invoices.current_page *
                                            invoices.per_page,
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
            </div>
        </div>

        <!-- Delete Confirmation Dialog -->
        <Dialog v-model:open="deleteDialogOpen">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Delete Client</DialogTitle>
                    <DialogDescription>
                        Are you sure you want to delete
                        <strong>{{ client.name }}</strong
                        >? This action cannot be undone and will also delete all
                        associated invoices.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter class="gap-2">
                    <DialogClose as-child>
                        <Button variant="secondary">Cancel</Button>
                    </DialogClose>
                    <Button
                        variant="destructive"
                        :disabled="deleting"
                        @click="deleteClient"
                    >
                        Delete
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
