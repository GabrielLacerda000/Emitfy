<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { User, Settings2, ListTree, LoaderCircle, Lock } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
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
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import UpgradeModal from '@/components/UpgradeModal.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { canChangeStatus } from '@/lib/featureGate';
import { toLocalDateInputValue } from '@/lib/utils';
import { index } from '@/routes/invoices';
import {
    type AppPageProps,
    type BreadcrumbItem,
    type Client,
    type Invoice,
    type InvoiceFormData,
} from '@/types';

const { t } = useI18n();
const page = usePage<AppPageProps>();

type Props = {
    invoice: Invoice;
    clients: Client[];
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('nav.invoices'),
        href: index().url,
    },
    {
        title: t('invoices.breadcrumbs.edit', { number: props.invoice.number }),
        href: '#',
    },
];

const formData = ref<InvoiceFormData>({
    client_id: props.invoice.client_id,
    issue_date: props.invoice.issue_date.split('T')[0],
    due_date: props.invoice.due_date.split('T')[0],
    tax: Number(props.invoice.tax),
    notes: props.invoice.notes ?? '',
    status: props.invoice.status,
    paid_at: props.invoice.paid_at ? props.invoice.paid_at.split('T')[0] : '',
    items:
        props.invoice.items?.map((item) => ({
            id: item.id,
            description: item.description,
            quantity: item.quantity,
            unit_price: Number(item.unit_price),
            total: Number(item.total),
        })) ?? [],
});

const features = page.props.features;
const upgradeModalOpen = ref(false);

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

// Auto-populate or clear paid_at when status changes
watch(
    () => formData.value.status,
    (newStatus, oldStatus) => {
        if (
            newStatus === 'paid' &&
            oldStatus !== 'paid' &&
            !formData.value.paid_at
        ) {
            // Status changed to paid and no paid_at exists - default to today
            formData.value.paid_at = toLocalDateInputValue();
        } else if (newStatus !== 'paid' && oldStatus === 'paid') {
            // Status changed from paid to something else - clear paid_at
            formData.value.paid_at = '';
        }
    },
);

function handleClientCreated(client: Client) {
    clients.value.push(client);
}

function handleStatusClick() {
    if (!canChangeStatus(features)) {
        upgradeModalOpen.value = true;
    }
}

function submitForm() {
    processing.value = true;
    errors.value = {};
    recentlySuccessful.value = false;
    console.log(formData.value);

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
        <div class="flex h-full flex-1 flex-col gap-8 bg-muted/20 p-6">
            <div class="mx-auto w-full max-w-4xl">
                <div
                    class="mb-8 flex flex-col justify-between gap-4 md:flex-row md:items-center"
                >
                    <Heading
                        :title="`${t('invoices.show.edit')} ${invoice.number}`"
                        :description="t('invoices.form.notesTerms')"
                    />
                    <div class="flex items-center gap-3">
                        <Button
                            variant="outline"
                            class="rounded-xl px-6"
                            as-child
                        >
                            <Link :href="index().url">{{
                                t('invoices.form.cancel')
                            }}</Link>
                        </Button>
                        <Button
                            @click="submitForm"
                            :disabled="processing"
                            class="rounded-xl bg-primary px-8 font-bold shadow-lg shadow-primary/20 transition-all hover:shadow-primary/30"
                        >
                            <LoaderCircle v-if="processing" class="mr-2" />
                            {{ t('invoices.form.updateInvoice') }}
                        </Button>
                    </div>
                </div>

                <form class="space-y-8" @submit.prevent="submitForm">
                    <div class="grid gap-6 md:grid-cols-3">
                        <Card
                            class="overflow-hidden rounded-3xl border-none bg-background shadow-sm md:col-span-2"
                        >
                            <div
                                class="border-b border-primary/5 bg-primary/5 px-6 py-4"
                            >
                                <h3
                                    class="flex items-center gap-2 text-[10px] font-black tracking-widest text-primary/70 uppercase"
                                >
                                    <span class="rounded-md bg-primary/10 p-1"
                                        ><User class="h-3 w-3"
                                    /></span>
                                    {{ t('invoices.form.clientBilling') }}
                                </h3>
                            </div>
                            <div class="space-y-6 p-6">
                                <div class="grid gap-2">
                                    <Label
                                        class="ml-1 text-[10px] font-black tracking-widest text-muted-foreground/80 uppercase"
                                    >
                                        {{ t('invoices.form.recipient') }}
                                    </Label>
                                    <ClientSelector
                                        v-model="formData.client_id"
                                        :clients="clients"
                                        :error="errors.client_id"
                                        @client-created="handleClientCreated"
                                    />
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="grid gap-2">
                                        <Label
                                            class="ml-1 text-[10px] font-black tracking-widest text-muted-foreground/80 uppercase"
                                        >
                                            {{ t('invoices.form.issueDate') }}
                                        </Label>
                                        <input
                                            v-model="formData.issue_date"
                                            type="date"
                                            class="h-11 rounded-xl border-border/60 bg-muted/20 px-4 text-sm transition-all outline-none focus:bg-background focus:ring-2 focus:ring-primary/20"
                                        />
                                        <InputError
                                            :message="errors.issue_date"
                                        />
                                    </div>
                                    <div class="grid gap-2">
                                        <Label
                                            class="ml-1 text-[10px] font-black tracking-widest text-muted-foreground/80 uppercase"
                                        >
                                            {{ t('invoices.form.dueDate') }}
                                        </Label>
                                        <input
                                            v-model="formData.due_date"
                                            type="date"
                                            class="h-11 rounded-xl border-border/60 bg-muted/20 px-4 text-sm transition-all outline-none focus:bg-background focus:ring-2 focus:ring-primary/20"
                                        />
                                        <InputError
                                            :message="errors.due_date"
                                        />
                                    </div>
                                </div>
                            </div>
                        </Card>

                        <Card
                            class="overflow-hidden rounded-3xl border-none bg-background shadow-sm"
                        >
                            <div
                                class="border-b border-primary/5 bg-primary/5 px-6 py-4"
                            >
                                <h3
                                    class="flex items-center gap-2 text-[10px] font-black tracking-widest text-primary/70 uppercase"
                                >
                                    <span class="rounded-md bg-primary/10 p-1"
                                        ><Settings2 class="h-3 w-3"
                                    /></span>
                                    {{ t('invoices.form.configuration') }}
                                </h3>
                            </div>
                            <div class="space-y-6 p-6">
                                <div class="grid gap-2">
                                    <Label
                                        class="ml-1 flex items-center gap-1.5 text-[10px] font-black tracking-widest text-muted-foreground/80 uppercase"
                                    >
                                        {{ t('invoices.form.status') }}
                                        <Lock
                                            v-if="!canChangeStatus(features)"
                                            class="h-3 w-3 text-muted-foreground/50"
                                        />
                                    </Label>
                                    <div @click.capture="handleStatusClick">
                                        <Select
                                            v-model="formData.status"
                                            :disabled="
                                                !canChangeStatus(features)
                                            "
                                        >
                                            <SelectTrigger
                                                class="h-11 rounded-xl border-border/60 bg-muted/20 transition-all focus:bg-background"
                                                :class="{
                                                    'cursor-not-allowed opacity-60':
                                                        !canChangeStatus(
                                                            features,
                                                        ),
                                                }"
                                            >
                                                <SelectValue />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="draft">{{
                                                    t('invoices.status.draft')
                                                }}</SelectItem>
                                                <SelectItem value="sent">{{
                                                    t('invoices.status.sent')
                                                }}</SelectItem>
                                                <SelectItem value="paid">{{
                                                    t('invoices.status.paid')
                                                }}</SelectItem>
                                                <SelectItem value="overdue">{{
                                                    t('invoices.status.overdue')
                                                }}</SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                    <InputError :message="errors.status" />
                                </div>

                                <div
                                    v-if="formData.status === 'paid'"
                                    class="grid gap-2"
                                >
                                    <Label
                                        class="ml-1 text-[10px] font-black tracking-widest text-muted-foreground/80 uppercase"
                                    >
                                        {{ t('invoices.form.paymentDate') }}
                                    </Label>
                                    <input
                                        v-model="formData.paid_at"
                                        type="date"
                                        :max="toLocalDateInputValue()"
                                        class="h-11 rounded-xl border-border/60 bg-muted/20 px-4 text-sm transition-all outline-none focus:bg-background"
                                    />
                                    <InputError :message="errors.paid_at" />
                                </div>

                                <div class="grid gap-2">
                                    <Label
                                        class="ml-1 text-[10px] font-black tracking-widest text-muted-foreground/80 uppercase"
                                    >
                                        {{ t('invoices.form.taxAmount') }}
                                    </Label>
                                    <NumberField
                                        v-model="formData.tax"
                                        :min="0"
                                        :step="0.01"
                                    >
                                        <NumberFieldContent>
                                            <NumberFieldInput
                                                class="h-11 rounded-xl border-border/60 bg-muted/20 transition-all focus:bg-background"
                                            />
                                        </NumberFieldContent>
                                    </NumberField>
                                    <InputError :message="errors.tax" />
                                </div>
                            </div>
                        </Card>
                    </div>

                    <Card
                        class="overflow-hidden rounded-3xl border-none bg-background shadow-sm"
                    >
                        <div
                            class="border-b border-primary/5 bg-primary/5 px-6 py-4"
                        >
                            <h3
                                class="flex items-center gap-2 text-[10px] font-black tracking-widest text-primary/70 uppercase"
                            >
                                <span class="rounded-md bg-primary/10 p-1"
                                    ><ListTree class="h-3 w-3"
                                /></span>
                                {{ t('invoices.form.invoiceItems') }}
                            </h3>
                        </div>
                        <div class="p-6">
                            <InvoiceItemsTable
                                v-model="formData.items"
                                :errors="errors"
                            />
                        </div>
                    </Card>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label
                                class="ml-1 text-[10px] font-black tracking-widest text-muted-foreground/80 uppercase"
                            >
                                {{ t('invoices.form.notesTerms') }}
                            </Label>
                            <textarea
                                v-model="formData.notes"
                                rows="5"
                                :placeholder="
                                    t('invoices.form.additionalDetails')
                                "
                                class="resize-none rounded-3xl border-border/60 bg-muted/20 p-4 text-sm transition-all outline-none focus:bg-background"
                            />
                        </div>

                        <div class="flex flex-col justify-end">
                            <InvoiceSummary
                                :subtotal="subtotal"
                                :tax="formData.tax"
                                :total="total"
                                class="rounded-3xl border-none bg-primary p-8 text-primary-foreground shadow-sm"
                            />
                            <div
                                v-if="recentlySuccessful"
                                class="mt-4 flex justify-end"
                            >
                                <span
                                    class="animate-in rounded-full bg-emerald-500/10 px-4 py-2 text-xs font-bold tracking-widest text-emerald-500 uppercase fade-in slide-in-from-bottom-2"
                                >
                                    {{ t('invoices.form.changesSaved') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <UpgradeModal v-model:open="upgradeModalOpen" />
    </AppLayout>
</template>
