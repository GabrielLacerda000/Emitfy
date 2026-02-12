<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Calendar,
    User,
    FileText,
    Percent,
    Save,
    Send,
    X,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';
import InvoiceController from '@/actions/App/Http/Controllers/InvoiceController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import ClientSelector from '@/components/invoices/ClientSelector.vue';
import InvoiceItemsTable from '@/components/invoices/InvoiceItemsTable.vue';
import InvoiceSummary from '@/components/invoices/InvoiceSummary.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import {
    NumberField,
    NumberFieldContent,
    NumberFieldInput,
} from '@/components/ui/number-field';
import AppLayout from '@/layouts/AppLayout.vue';
import { index } from '@/routes/invoices';
import type { CreateInvoiceData, BreadcrumbItem, Client } from '@/types';

type Props = {
    clients: Client[];
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Invoices', href: index().url },
    { title: 'New Invoice', href: '#' },
];

const formData = ref<CreateInvoiceData>({
    client_id: null,
    issue_date: new Date().toISOString().split('T')[0],
    due_date: new Date().toISOString().split('T')[0],
    tax: 0,
    notes: '',
    status: 'draft',
    items: [{ description: '', quantity: 1, unit_price: 0, total: 0 }],
});

const errors = ref<Record<string, string>>({});
const processing = ref(false);

const subtotal = computed(() => {
    return formData.value.items
        .reduce((sum, item) => sum + (Number(item.total) || 0), 0)
        .toFixed(2);
});

const total = computed(() => {
    return (Number(subtotal.value) + Number(formData.value.tax)).toFixed(2);
});

const clients = ref(props.clients);

function handleClientCreated(client: Client) {
    clients.value.push(client);
}

function submitForm(status: 'draft' | 'sent') {
    formData.value.status = status;
    processing.value = true;
    errors.value = {};

    router.post(InvoiceController.store.url(), formData.value, {
        onError: (errs) => {
            errors.value = errs;
        },
        onFinish: () => {
            processing.value = false;
        },
    });
}

// Estilo comum para as labels (mesmo estilo do cabeçalho da tabela)
const labelClass =
    'flex items-center gap-2 text-[10px] font-black tracking-[0.2em] text-muted-foreground/80 uppercase mb-2';
// Estilo comum para os cards
const cardClass =
    'rounded-2xl border border-border/60 bg-background shadow-sm overflow-hidden';
</script>

<template>
    <Head title="New Invoice" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 bg-muted/5 p-6">
            <div class="mx-auto w-full max-w-4xl">
                <div class="mb-8 flex flex-col gap-2">
                    <Heading
                        title="New Invoice"
                        description="Craft a beautiful invoice for your client"
                    />
                </div>

                <form class="space-y-8" @submit.prevent>
                    <div :class="cardClass">
                        <div
                            class="border-b border-border/60 bg-muted/30 px-6 py-4"
                        >
                            <h3
                                class="flex items-center gap-2 text-sm font-bold tracking-tight"
                            >
                                <User class="h-4 w-4 text-primary" /> Client
                                Details & Timeline
                            </h3>
                        </div>

                        <div class="grid gap-8 p-6 md:grid-cols-2">
                            <div class="grid gap-2 md:col-span-2">
                                <Label :class="labelClass">Select Client</Label>
                                <ClientSelector
                                    v-model="formData.client_id"
                                    :clients="clients"
                                    :error="errors.client_id"
                                    @client-created="handleClientCreated"
                                />
                            </div>

                            <div class="grid gap-2">
                                <Label :class="labelClass" for="issue_date">
                                    <Calendar class="h-3 w-3" /> Issue Date
                                </Label>
                                <input
                                    id="issue_date"
                                    v-model="formData.issue_date"
                                    type="date"
                                    required
                                    class="h-11 w-full rounded-xl border border-border/40 bg-muted/20 px-4 text-sm transition-all outline-none focus:bg-background focus:ring-2 focus:ring-primary/20"
                                />
                                <InputError :message="errors.issue_date" />
                            </div>

                            <div class="grid gap-2">
                                <Label :class="labelClass" for="due_date">
                                    <Calendar class="h-3 w-3" /> Due Date
                                </Label>
                                <input
                                    id="due_date"
                                    v-model="formData.due_date"
                                    type="date"
                                    required
                                    class="h-11 w-full rounded-xl border border-border/40 bg-muted/20 px-4 text-sm transition-all outline-none focus:bg-background focus:ring-2 focus:ring-primary/20"
                                />
                                <InputError :message="errors.due_date" />
                            </div>
                        </div>
                    </div>

                    <div :class="cardClass">
                        <div
                            class="border-b border-border/60 bg-muted/30 px-6 py-4"
                        >
                            <h3
                                class="flex items-center gap-2 text-sm font-bold tracking-tight"
                            >
                                <FileText class="h-4 w-4 text-primary" />
                                Invoice Items
                            </h3>
                        </div>
                        <div class="p-0">
                            <InvoiceItemsTable
                                v-model="formData.items"
                                :errors="errors"
                            />
                        </div>
                        <div
                            v-if="errors.items"
                            class="border-t border-destructive/20 bg-destructive/5 p-4 text-center"
                        >
                            <InputError :message="errors.items" />
                        </div>
                    </div>

                    <div class="grid gap-8 md:grid-cols-5">
                        <div class="space-y-8 md:col-span-3">
                            <div :class="cardClass">
                                <div
                                    class="border-b border-border/60 bg-muted/30 px-6 py-4"
                                >
                                    <Label :class="labelClass" class="mb-0"
                                        >Notes & Terms</Label
                                    >
                                </div>
                                <div class="p-6">
                                    <textarea
                                        id="notes"
                                        v-model="formData.notes"
                                        rows="5"
                                        placeholder="Add any specific instructions or thank you notes..."
                                        class="w-full resize-none rounded-xl border border-border/40 bg-muted/20 p-4 text-sm transition-all outline-none focus:bg-background focus:ring-2 focus:ring-primary/20"
                                    />
                                    <InputError :message="errors.notes" />
                                </div>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <div :class="cardClass" class="h-full">
                                <div
                                    class="border-b border-border/60 bg-muted/30 px-6 py-4"
                                >
                                    <Label :class="labelClass" class="mb-0"
                                        >Financial Summary</Label
                                    >
                                </div>
                                <div class="space-y-6 p-6">
                                    <div class="grid gap-2">
                                        <Label :class="labelClass" for="tax">
                                            <Percent class="h-3 w-3" />
                                            Adjustments / Tax
                                        </Label>
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
                                                    class="h-11 rounded-xl border-border/40 bg-muted/20 text-center font-bold focus:bg-background"
                                                />
                                            </NumberFieldContent>
                                        </NumberField>
                                        <InputError :message="errors.tax" />
                                    </div>

                                    <div class="border-t border-border/60 pt-4">
                                        <InvoiceSummary
                                            :subtotal="subtotal"
                                            :tax="formData.tax"
                                            :total="total"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="mt-4 flex items-center justify-between border-t border-border/60 pt-8"
                    >
                        <Button
                            variant="ghost"
                            as-child
                            class="h-12 rounded-xl px-6 transition-colors hover:bg-destructive/5 hover:text-destructive"
                        >
                            <Link :href="index().url">
                                <X class="mr-2 h-4 w-4" /> Cancel
                            </Link>
                        </Button>

                        <div class="flex items-center gap-3">
                            <Button
                                type="button"
                                variant="outline"
                                class="h-12 rounded-xl border-2 px-6 font-bold transition-all hover:bg-muted"
                                @click="submitForm('draft')"
                                :disabled="processing"
                            >
                                <Save class="mr-2 h-4 w-4" /> Draft
                            </Button>

                            <Button
                                type="button"
                                class="h-12 rounded-xl bg-primary px-8 text-primary-foreground shadow-lg shadow-primary/20 transition-all hover:-translate-y-0.5 hover:shadow-primary/30"
                                @click="submitForm('sent')"
                                :disabled="processing"
                            >
                                <Send class="mr-2 h-4 w-4" /> Send
                            </Button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
