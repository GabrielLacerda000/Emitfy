<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import InvoiceController from '@/actions/App/Http/Controllers/InvoiceController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import ClientSelector from '@/components/invoices/ClientSelector.vue';
import InvoiceItemsTable from '@/components/invoices/InvoiceItemsTable.vue';
import InvoiceSummary from '@/components/invoices/InvoiceSummary.vue';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { index } from '@/routes/invoices';
import { type BreadcrumbItem, type Client, type Invoice, type InvoiceFormData } from '@/types';

type Props = {
    invoice: Invoice;
    clients: Client[];
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Invoices',
        href: index().url,
    },
    {
        title: `Edit ${props.invoice.number}`,
        href: '#',
    },
];

const formData = ref<InvoiceFormData>({
    client_id: props.invoice.client_id,
    issue_date: props.invoice.issue_date,
    due_date: props.invoice.due_date,
    tax: props.invoice.tax,
    notes: props.invoice.notes ?? '',
    status: props.invoice.status,
    items:
        props.invoice.items?.map((item) => ({
            id: item.id,
            description: item.description,
            quantity: item.quantity,
            unit_price: item.unit_price,
            total: item.total,
        })) ?? [],
});

const errors = ref<Record<string, string>>({});
const processing = ref(false);
const recentlySuccessful = ref(false);

const subtotal = computed(() => {
    return formData.value.items
        .reduce((sum, item) => {
            return sum + (Number(item.total) || 0);
        }, 0)
        .toFixed(2);
});

const total = computed(() => {
    return (Number(subtotal.value) + Number(formData.value.tax)).toFixed(2);
});

const clients = ref(props.clients);

function handleClientCreated(client: Client) {
    clients.value.push(client);
}

function submitForm() {
    processing.value = true;
    errors.value = {};
    recentlySuccessful.value = false;

    router.put(
        InvoiceController.update.url({ invoice: props.invoice.id }),
        formData.value,
        {
            preserveScroll: true,
            onError: (errs) => {
                errors.value = errs;
            },
            onSuccess: () => {
                recentlySuccessful.value = true;
                setTimeout(() => {
                    recentlySuccessful.value = false;
                }, 2000);
            },
            onFinish: () => {
                processing.value = false;
            },
        },
    );
}
</script>

<template>
    <Head :title="`Edit ${invoice.number}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="mx-auto w-full max-w-4xl">
                <Heading
                    :title="`Edit Invoice ${invoice.number}`"
                    description="Update invoice details and line items"
                />

                <form class="mt-6 space-y-6" @submit.prevent="submitForm">
                    <!-- Client & Dates -->
                    <Card class="p-6">
                        <h3 class="mb-4 text-lg font-semibold">Client & Dates</h3>
                        <div class="grid gap-6 md:grid-cols-2">
                            <div class="grid gap-2 md:col-span-2">
                                <Label for="client_id">Client</Label>
                                <ClientSelector
                                    v-model="formData.client_id"
                                    :clients="clients"
                                    :error="errors.client_id"
                                    @client-created="handleClientCreated"
                                />
                            </div>

                            <div class="grid gap-2">
                                <Label for="issue_date">Issue Date</Label>
                                <input
                                    id="issue_date"
                                    v-model="formData.issue_date"
                                    type="date"
                                    required
                                    class="placeholder:text-muted-foreground dark:bg-input/30 border-input w-full min-w-0 rounded-md border bg-transparent px-3 py-2 text-base shadow-xs transition-[color,box-shadow] outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                                />
                                <InputError :message="errors.issue_date" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="due_date">Due Date</Label>
                                <input
                                    id="due_date"
                                    v-model="formData.due_date"
                                    type="date"
                                    required
                                    class="placeholder:text-muted-foreground dark:bg-input/30 border-input w-full min-w-0 rounded-md border bg-transparent px-3 py-2 text-base shadow-xs transition-[color,box-shadow] outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                                />
                                <InputError :message="errors.due_date" />
                            </div>
                        </div>
                    </Card>

                    <!-- Status -->
                    <Card class="p-6">
                        <h3 class="mb-4 text-lg font-semibold">Status</h3>
                        <div class="grid gap-2">
                            <Label for="status">Invoice Status</Label>
                            <select
                                id="status"
                                v-model="formData.status"
                                required
                                class="placeholder:text-muted-foreground dark:bg-input/30 border-input w-full min-w-0 rounded-md border bg-transparent px-3 py-2 text-base shadow-xs transition-[color,box-shadow] outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                            >
                                <option value="draft">Draft</option>
                                <option value="sent">Sent</option>
                                <option value="paid">Paid</option>
                                <option value="overdue">Overdue</option>
                            </select>
                            <InputError :message="errors.status" />
                        </div>
                    </Card>

                    <!-- Line Items -->
                    <Card class="p-6">
                        <h3 class="mb-4 text-lg font-semibold">Line Items</h3>
                        <InvoiceItemsTable
                            v-model="formData.items"
                            :errors="errors"
                        />
                        <InputError :message="errors.items" class="mt-2" />
                    </Card>

                    <!-- Additional Details -->
                    <Card class="p-6">
                        <h3 class="mb-4 text-lg font-semibold">Additional Details</h3>
                        <div class="space-y-6">
                            <div class="grid gap-2">
                                <Label for="tax">Tax Amount</Label>
                                <Input
                                    id="tax"
                                    v-model="formData.tax"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    required
                                    placeholder="0.00"
                                />
                                <InputError :message="errors.tax" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="notes">Notes</Label>
                                <textarea
                                    id="notes"
                                    v-model="formData.notes"
                                    rows="4"
                                    placeholder="Additional notes for this invoice (optional)"
                                    class="placeholder:text-muted-foreground dark:bg-input/30 border-input w-full min-w-0 rounded-md border bg-transparent px-3 py-2 text-base shadow-xs transition-[color,box-shadow] outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                                />
                                <InputError :message="errors.notes" />
                            </div>
                        </div>
                    </Card>

                    <!-- Summary -->
                    <InvoiceSummary
                        :subtotal="subtotal"
                        :tax="formData.tax"
                        :total="total"
                    />

                    <!-- Actions -->
                    <div class="flex items-center gap-4">
                        <Button type="submit" :disabled="processing">
                            Update Invoice
                        </Button>
                        <Button variant="ghost" as-child>
                            <Link :href="index().url">Cancel</Link>
                        </Button>
                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p v-if="recentlySuccessful" class="text-sm text-green-600 dark:text-green-400">
                                Saved.
                            </p>
                        </Transition>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
