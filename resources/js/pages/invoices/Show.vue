<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Download, Pencil, Trash2, Eye } from 'lucide-vue-next';
import { ref } from 'vue';
import InvoiceController from '@/actions/App/Http/Controllers/InvoiceController';
import InvoicePdfController from '@/actions/App/Http/Controllers/InvoicePdfController';
import InvoiceItemsTable from '@/components/invoices/InvoiceItemsTable.vue';
import InvoiceSummary from '@/components/invoices/InvoiceSummary.vue';
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
import AppLayout from '@/layouts/AppLayout.vue';
import { formatDate } from '@/lib/utils';
import { edit, index } from '@/routes/invoices';
import { type BreadcrumbItem, type Invoice } from '@/types';

type Props = {
    invoice: Invoice;
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Invoices',
        href: index().url,
    },
    {
        title: props.invoice.number,
        href: '#',
    },
];

function getStatusConfig(status: string) {
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
    return configs[status as keyof typeof configs];
}

const deleteDialogOpen = ref(false);
const deleting = ref(false);
const downloadingPdf = ref(false);

function confirmDelete() {
    deleteDialogOpen.value = true;
}

function deleteInvoice() {
    deleting.value = true;
    router.delete(
        InvoiceController.destroy.url({ invoice: props.invoice.id }),
        {
            onFinish: () => {
                deleting.value = false;
            },
        },
    );
}

function downloadPdf() {
    downloadingPdf.value = true;
    const url = InvoicePdfController.show.url({ invoice: props.invoice.id });
    window.location.href = url;
    setTimeout(() => {
        downloadingPdf.value = false;
    }, 2000);
}

function viewPdf() {
    const baseUrl = InvoicePdfController.show.url({
        invoice: props.invoice.id,
    });
    const url = `${baseUrl}?mode=stream`;

    // Abre em uma nova aba
    window.open(url, '_blank');
}
</script>

<template>
    <Head :title="`Invoice ${invoice.number}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="mx-auto w-full max-w-4xl">
                <!-- Header -->
                <div class="flex items-start justify-between">
                    <div>
                        <div class="flex items-center gap-3">
                            <h1 class="text-2xl font-bold">
                                Invoice {{ invoice.number }}
                            </h1>
                            <Badge
                                :variant="
                                    getStatusConfig(invoice.status).variant
                                "
                                :class="getStatusConfig(invoice.status).class"
                            >
                                {{ getStatusConfig(invoice.status).label }}
                            </Badge>
                        </div>
                        <p class="mt-1 text-sm text-muted-foreground">
                            Created {{ formatDate(invoice.created_at) }}
                        </p>
                    </div>

                    <div class="flex gap-2">
                        <Button variant="outline" as-child>
                            <Link :href="edit({ invoice: invoice.id }).url">
                                <Pencil class="mr-2 h-4 w-4" />
                                Edit
                            </Link>
                        </Button>
                        <Button variant="outline" @click="viewPdf">
                            <Eye class="mr-2 h-4 w-4" />
                            View PDF
                        </Button>
                        <Button
                            variant="outline"
                            :disabled="downloadingPdf"
                            @click="downloadPdf"
                        >
                            <Download class="mr-2 h-4 w-4" />
                            {{
                                downloadingPdf
                                    ? 'Downloading...'
                                    : 'Download PDF'
                            }}
                        </Button>
                        <Button variant="destructive" @click="confirmDelete">
                            <Trash2 class="mr-2 h-4 w-4" />
                            Delete
                        </Button>
                    </div>
                </div>

                <!-- Invoice Details -->
                <div class="mt-6 grid gap-6 md:grid-cols-2">
                    <!-- Invoice Info -->
                    <Card class="p-6">
                        <h3 class="mb-4 text-lg font-semibold">
                            Invoice Details
                        </h3>
                        <dl class="space-y-3 text-sm">
                            <div>
                                <dt class="font-medium text-muted-foreground">
                                    Issue Date
                                </dt>
                                <dd class="mt-1">
                                    {{ formatDate(invoice.issue_date) }}
                                </dd>
                            </div>
                            <div>
                                <dt class="font-medium text-muted-foreground">
                                    Due Date
                                </dt>
                                <dd class="mt-1">
                                    {{ formatDate(invoice.due_date) }}
                                </dd>
                            </div>
                            <div v-if="invoice.sent_at">
                                <dt class="font-medium text-muted-foreground">
                                    Sent On
                                </dt>
                                <dd class="mt-1">
                                    {{ formatDate(invoice.sent_at) }}
                                </dd>
                            </div>
                            <div v-if="invoice.paid_at">
                                <dt class="font-medium text-muted-foreground">
                                    Paid On
                                </dt>
                                <dd class="mt-1">
                                    {{ formatDate(invoice.paid_at) }}
                                </dd>
                            </div>
                        </dl>
                    </Card>

                    <!-- Client Info -->
                    <Card class="p-6">
                        <h3 class="mb-4 text-lg font-semibold">Client</h3>
                        <dl class="space-y-3 text-sm">
                            <div>
                                <dt class="font-medium text-muted-foreground">
                                    Name
                                </dt>
                                <dd class="mt-1">{{ invoice.client.name }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-muted-foreground">
                                    Email
                                </dt>
                                <dd class="mt-1">{{ invoice.client.email }}</dd>
                            </div>
                            <div v-if="invoice.client.company_name">
                                <dt class="font-medium text-muted-foreground">
                                    Company
                                </dt>
                                <dd class="mt-1">
                                    {{ invoice.client.company_name }}
                                </dd>
                            </div>
                        </dl>
                    </Card>
                </div>

                <!-- Line Items -->
                <Card class="mt-6 p-6">
                    <h3 class="mb-4 text-lg font-semibold">Line Items</h3>
                    <InvoiceItemsTable
                        :model-value="invoice.items ?? []"
                        readonly
                    />
                </Card>

                <!-- Notes -->
                <Card v-if="invoice.notes" class="mt-6 p-6">
                    <h3 class="mb-4 text-lg font-semibold">Notes</h3>
                    <p
                        class="text-sm whitespace-pre-wrap text-muted-foreground"
                    >
                        {{ invoice.notes }}
                    </p>
                </Card>

                <!-- Summary -->
                <div class="mt-6">
                    <InvoiceSummary
                        :subtotal="invoice.subtotal"
                        :tax="invoice.tax"
                        :total="invoice.total"
                    />
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
                        <strong>{{ invoice.number }}</strong
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
