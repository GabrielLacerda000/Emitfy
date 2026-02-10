<script setup lang="ts">
import { Download } from 'lucide-vue-next';
import { ref } from 'vue';
import { toast } from 'vue-sonner'
import InvoicePdfController from '@/actions/App/Http/Controllers/InvoicePdfController';
import InvoiceItemsTable from '@/components/invoices/InvoiceItemsTable.vue';
import InvoiceSummary from '@/components/invoices/InvoiceSummary.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { formatDate } from '@/lib/utils';
import { type Invoice } from '@/types';

type Props = {
    invoice: Invoice;
};

const props = defineProps<Props>();

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

const downloadingPdf = ref(false);

function downloadPdf() {
    downloadingPdf.value = true;
    const url = InvoicePdfController.show.url({ invoice: props.invoice.id });
    window.location.href = url;
    setTimeout(() => {
        downloadingPdf.value = false;
    }, 2000);
}

function payNow() {
    toast.info('Payment integration will be available soon.');
}
</script>

<template>
    <PublicLayout :title="`Invoice ${invoice.number}`">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold">Invoice {{ invoice.number }}</h1>
                <Badge
                    :variant="getStatusConfig(invoice.status).variant"
                    :class="getStatusConfig(invoice.status).class"
                >
                    {{ getStatusConfig(invoice.status).label }}
                </Badge>
            </div>
            <p class="mt-1 text-sm text-muted-foreground">
                Issued {{ formatDate(invoice.issue_date) }}
            </p>
        </div>

        <!-- Invoice Details Grid -->
        <div class="grid gap-6 md:grid-cols-2">
            <!-- Invoice Info -->
            <Card class="p-6">
                <h3 class="mb-4 text-lg font-semibold">Invoice Details</h3>
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
                        <dd class="mt-1">{{ formatDate(invoice.due_date) }}</dd>
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
                <h3 class="mb-4 text-lg font-semibold">Bill To</h3>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="font-medium text-muted-foreground">Name</dt>
                        <dd class="mt-1">{{ invoice.client.name }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-muted-foreground">Email</dt>
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
            <InvoiceItemsTable :model-value="invoice.items ?? []" readonly />
        </Card>

        <!-- Notes -->
        <Card v-if="invoice.notes" class="mt-6 p-6">
            <h3 class="mb-4 text-lg font-semibold">Notes</h3>
            <p class="whitespace-pre-wrap text-sm text-muted-foreground">
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

        <!-- Action Buttons -->
        <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:justify-end">
            <Button
                variant="outline"
                :disabled="downloadingPdf"
                @click="downloadPdf"
            >
                <Download class="mr-2 h-4 w-4" />
                {{ downloadingPdf ? 'Downloading...' : 'Download PDF' }}
            </Button>
            <Button
                :disabled="invoice.status === 'paid'"
                @click="payNow"
            >
                {{ invoice.status === 'paid' ? 'Paid' : 'Pay Now' }}
            </Button>
        </div>
    </PublicLayout>
</template>
