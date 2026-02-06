<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import InvoiceController from '@/actions/App/Http/Controllers/InvoiceController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import ClientSelector from '@/components/invoices/ClientSelector.vue';
import InvoiceItemsTable from '@/components/invoices/InvoiceItemsTable.vue';
import InvoiceSummary from '@/components/invoices/InvoiceSummary.vue';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import {
    NumberField,
    NumberFieldContent,
    NumberFieldInput,
} from '@/components/ui/number-field';
import AppLayout from '@/layouts/AppLayout.vue';
import { index } from '@/routes/invoices';
import {
    type BreadcrumbItem,
    type Client,
    type InvoiceFormData,
} from '@/types';

type Props = {
    clients: Client[];
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Invoices',
        href: index().url,
    },
    {
        title: 'New Invoice',
        href: '#',
    },
];

const formData = ref<InvoiceFormData>({
    client_id: null,
    issue_date: new Date().toISOString().split('T')[0],
    due_date: new Date().toISOString().split('T')[0],
    tax: 0,
    notes: '',
    status: 'draft',
    items: [
        {
            description: '',
            quantity: 1,
            unit_price: 0,
            total: 0,
        },
    ],
});

const errors = ref<Record<string, string>>({});
const processing = ref(false);

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

// Debug: Watch client_id changes
watch(
    () => formData.value.client_id,
    (newValue, oldValue) => {
        console.log('[Create] client_id changed:', {
            oldValue,
            newValue,
            type: typeof newValue,
        });
    },
);

function handleClientCreated(client: Client) {
    clients.value.push(client);
}

function submitForm(status: 'draft' | 'sent') {
    formData.value.status = status;
    processing.value = true;
    errors.value = {};

    router.post(InvoiceController.store.url(), formData.value, {
        onError: (errs) => {
            console.log('[Create] Submission errors:', errs);
            errors.value = errs;
        },
        onFinish: () => {
            processing.value = false;
        },
    });
}
</script>

<template>
    <Head title="New Invoice" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="mx-auto w-full max-w-4xl">
                <Heading
                    title="New Invoice"
                    description="Create a new invoice for a client"
                />

                <form class="mt-6 space-y-6" @submit.prevent>
                    <!-- Client & Dates -->
                    <Card class="p-6">
                        <h3 class="mb-4 text-lg font-semibold">
                            Client & Dates
                        </h3>
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
                                    class="w-full min-w-0 rounded-md border border-input bg-transparent px-3 py-2 text-base shadow-xs transition-[color,box-shadow] outline-none placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm dark:bg-input/30"
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
                                    class="w-full min-w-0 rounded-md border border-input bg-transparent px-3 py-2 text-base shadow-xs transition-[color,box-shadow] outline-none placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm dark:bg-input/30"
                                />
                                <InputError :message="errors.due_date" />
                            </div>
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
                        <h3 class="mb-4 text-lg font-semibold">
                            Additional Details
                        </h3>
                        <div class="space-y-6">
                            <div class="grid gap-2">
                                <Label for="tax">Tax Amount</Label>
                                <NumberField
                                    v-model="formData.tax"
                                    :min="0"
                                    :step="0.01"
                                    :format-options="{
                                        style: 'currency',
                                        currency: 'BRL',
                                    }"
                                >
                                    <NumberFieldContent>
                                        <NumberFieldInput
                                            id="tax"
                                            placeholder="R$ 0,00"
                                        />
                                    </NumberFieldContent>
                                </NumberField>
                                <InputError :message="errors.tax" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="notes">Notes</Label>
                                <textarea
                                    id="notes"
                                    v-model="formData.notes"
                                    rows="4"
                                    placeholder="Additional notes for this invoice (optional)"
                                    class="w-full min-w-0 rounded-md border border-input bg-transparent px-3 py-2 text-base shadow-xs transition-[color,box-shadow] outline-none placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm dark:bg-input/30"
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
                        <Button
                            type="button"
                            @click="submitForm('draft')"
                            :disabled="processing"
                        >
                            Save as Draft
                        </Button>
                        <Button
                            type="button"
                            variant="default"
                            @click="submitForm('sent')"
                            :disabled="processing"
                            class="cursor-pointer"
                        >
                            Save & Send
                        </Button>
                        <Button variant="ghost" as-child>
                            <Link :href="index().url">Cancel</Link>
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
