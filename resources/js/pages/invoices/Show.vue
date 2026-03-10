<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import {
    Download,
    Mail,
    Pencil,
    Trash2,
    Eye,
    Calendar,
    User,
    FileText,
    CheckCircle2,
    AlertCircle,
    Clock,
    ArrowLeft,
    History,
    Building2,
} from 'lucide-vue-next';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import InvoiceController from '@/actions/App/Http/Controllers/InvoiceController';
import InvoicePdfController from '@/actions/App/Http/Controllers/InvoicePdfController';
import InvoiceItemsTable from '@/components/invoices/InvoiceItemsTable.vue';
import InvoiceSummary from '@/components/invoices/InvoiceSummary.vue';
import UpgradeModal from '@/components/UpgradeModal.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
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
import { canSendInvoice, canViewPdf } from '@/lib/featureGate';
import { formatDate } from '@/lib/utils';
import { edit, index } from '@/routes/invoices';
import { type AppPageProps, type BreadcrumbItem, type Invoice } from '@/types';

const { t } = useI18n();
const page = usePage<AppPageProps>();

type Props = {
    invoice: Invoice;
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: t('nav.invoices'), href: index().url },
    { title: props.invoice.number, href: '#' },
];

function getStatusConfig(status: string) {
    const configs = {
        draft: {
            variant: 'secondary' as const,
            label: t('invoices.status.draft'),
            icon: FileText,
            class: 'bg-slate-100 text-slate-600 border-slate-200',
        },
        sent: {
            variant: 'default' as const,
            label: t('invoices.status.sent'),
            icon: Clock,
            class: 'bg-blue-50 text-blue-600 border-blue-200',
        },
        paid: {
            variant: 'outline' as const,
            label: t('invoices.status.paid'),
            icon: CheckCircle2,
            class: 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10',
        },
        overdue: {
            variant: 'destructive' as const,
            label: t('invoices.status.overdue'),
            icon: AlertCircle,
            class: 'animate-pulse',
        },
    };
    return configs[status as keyof typeof configs] || configs.draft;
}

const features = page.props.features;

const deleteDialogOpen = ref(false);
const upgradeModalOpen = ref(false);
const deleting = ref(false);
const downloadingPdf = ref(false);
const sending = ref(false);

function deleteInvoice() {
    deleting.value = true;
    router.delete(
        InvoiceController.destroy.url({ invoice: props.invoice.id }),
        {
            onFinish: () => (deleting.value = false),
        },
    );
}

function downloadPdf() {
    if (!canViewPdf(features)) {
        upgradeModalOpen.value = true;
        return;
    }
    downloadingPdf.value = true;
    window.location.href = InvoicePdfController.show.url({
        invoice: props.invoice.id,
    });
    setTimeout(() => (downloadingPdf.value = false), 2000);
}

function viewPdf() {
    if (!canViewPdf(features)) {
        upgradeModalOpen.value = true;
        return;
    }
    const url = `${InvoicePdfController.show.url({ invoice: props.invoice.id })}?mode=stream`;
    window.open(url, '_blank');
}

function sendInvoice() {
    if (!canSendInvoice(features)) {
        upgradeModalOpen.value = true;
        return;
    }
    sending.value = true;
    router.post(
        InvoiceController.send.url({ invoice: props.invoice.id }),
        {},
        {
            onFinish: () => (sending.value = false),
        },
    );
}
</script>

<template>
    <Head :title="`Invoice ${invoice.number}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex min-h-screen flex-col gap-6 bg-muted/20 p-6 lg:p-8">
            <div class="mx-auto w-full max-w-5xl">
                <div
                    class="mb-6 flex flex-col justify-between gap-4 md:flex-row md:items-center"
                >
                    <div class="flex items-center gap-4">
                        <Link
                            :href="index().url"
                            class="rounded-full border border-transparent p-2 transition-colors hover:border-border hover:bg-background"
                        >
                            <ArrowLeft class="h-5 w-5" />
                        </Link>
                        <div>
                            <div class="flex items-center gap-3">
                                <h1
                                    class="text-2xl font-black tracking-tight uppercase italic"
                                >
                                    {{ invoice.number }}
                                </h1>
                                <Badge
                                    :class="[
                                        getStatusConfig(invoice.status).class,
                                        'flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold tracking-wider uppercase',
                                    ]"
                                >
                                    <component
                                        :is="
                                            getStatusConfig(invoice.status).icon
                                        "
                                        class="h-3 w-3"
                                    />
                                    {{ getStatusConfig(invoice.status).label }}
                                </Badge>
                            </div>
                            <p
                                class="mt-0.5 text-sm font-medium text-muted-foreground"
                            >
                                {{ t('invoices.show.createdOn') }} {{ formatDate(invoice.created_at) }}
                            </p>
                        </div>
                    </div>

                    <div
                        class="flex flex-wrap items-center gap-2 rounded-2xl border border-border/60 bg-background p-1.5 shadow-sm"
                    >
                        <Button
                            variant="ghost"
                            size="sm"
                            class="text-xs font-bold"
                            as-child
                        >
                            <Link :href="edit({ invoice: invoice.id }).url">
                                <Pencil class="mr-2 h-3.5 w-3.5 text-primary" />
                                {{ t('invoices.show.edit') }}
                            </Link>
                        </Button>
                        <div class="mx-1 h-4 w-px bg-border" />
                        <Button
                            variant="ghost"
                            size="sm"
                            class="text-xs font-bold"
                            @click="viewPdf"
                        >
                            <Eye
                                class="mr-2 h-3.5 w-3.5 text-muted-foreground"
                            />
                            {{ t('invoices.show.viewPdf') }}
                        </Button>
                        <Button
                            variant="ghost"
                            size="sm"
                            class="text-xs font-bold"
                            :disabled="downloadingPdf"
                            @click="downloadPdf"
                        >
                            <Download
                                class="mr-2 h-3.5 w-3.5 text-muted-foreground"
                            />
                            {{ downloadingPdf ? '...' : t('invoices.show.download') }}
                        </Button>
                        <Button
                            variant="destructive"
                            size="sm"
                            class="h-8 rounded-xl text-xs font-bold"
                            @click="deleteDialogOpen = true"
                        >
                            <Trash2 class="h-3.5 w-3.5" />
                        </Button>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <div class="space-y-6 lg:col-span-2">
                        <Card
                            class="overflow-hidden rounded-3xl border-none shadow-sm"
                        >
                            <CardHeader class="border-b bg-muted/10 pb-4">
                                <CardTitle
                                    class="flex items-center gap-2 text-sm font-bold tracking-widest text-muted-foreground uppercase"
                                >
                                    <FileText class="h-4 w-4" /> {{ t('invoices.show.servicesRendered') }}
                                </CardTitle>
                            </CardHeader>
                            <CardContent class="p-0">
                                <InvoiceItemsTable
                                    :model-value="invoice.items ?? []"
                                    readonly
                                />
                            </CardContent>
                            <div class="border-t bg-muted/5 p-6">
                                <InvoiceSummary
                                    :subtotal="invoice.subtotal"
                                    :tax="invoice.tax"
                                    :total="invoice.total"
                                />
                            </div>
                        </Card>

                        <Card
                            v-if="invoice.notes"
                            class="rounded-3xl border-none shadow-sm"
                        >
                            <CardHeader>
                                <CardTitle
                                    class="text-sm font-bold tracking-widest text-muted-foreground uppercase"
                                >
                                    {{ t('invoices.show.internalNotes') }}
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <p
                                    class="border-l-4 border-primary/20 pl-4 text-sm leading-relaxed text-muted-foreground italic"
                                >
                                    "{{ invoice.notes }}"
                                </p>
                            </CardContent>
                        </Card>
                    </div>

                    <div class="space-y-6">
                        <Button
                            class="text-md h-14 w-full rounded-2xl font-black shadow-xl shadow-primary/20 transition-all hover:-translate-y-1 hover:shadow-primary/30"
                            :disabled="sending || invoice.status === 'paid'"
                            @click="sendInvoice"
                        >
                            <Mail class="mr-3 h-5 w-5" />
                            {{
                                invoice.status === 'draft'
                                    ? t('invoices.show.sendToClient')
                                    : t('invoices.show.resendInvoice')
                            }}
                        </Button>

                        <Card
                            class="group overflow-hidden rounded-3xl border-none bg-background shadow-sm"
                        >
                            <CardHeader class="border-b border-primary/5 pb-4">
                                <CardTitle
                                    class="flex items-center gap-2 text-[10px] font-black tracking-[0.2em] text-primary/70 uppercase"
                                >
                                    <User
                                        class="h-3.5 w-3.5 transition-transform group-hover:scale-110"
                                    />
                                    {{ t('invoices.show.recipientDetails') }}
                                </CardTitle>
                            </CardHeader>

                            <CardContent class="space-y-4 pt-6">
                                <div>
                                    <div
                                        class="text-xl leading-none font-black tracking-tight text-foreground"
                                    >
                                        {{ invoice.client.name }}
                                    </div>
                                    <div
                                        class="mt-2 flex items-center gap-2 text-sm font-bold text-muted-foreground italic"
                                    >
                                        <Mail class="h-3 w-3 opacity-70" />
                                        {{ invoice.client.email }}
                                    </div>
                                </div>

                                <div
                                    v-if="invoice.client.company_name"
                                    class="relative mt-4 flex items-center gap-3 overflow-hidden rounded-2xl border border-border/20 bg-muted/30 p-4"
                                >
                                    <div
                                        class="absolute top-0 right-0 h-full w-1 bg-primary/20"
                                    />

                                    <div
                                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-background shadow-sm"
                                    >
                                        <Building2
                                            class="h-4 w-4 text-primary/60"
                                        />
                                    </div>

                                    <div class="flex flex-col">
                                        <span
                                            class="mb-1 text-[9px] leading-none font-black tracking-widest text-muted-foreground/50 uppercase"
                                        >
                                            {{ t('invoices.show.organization') }}
                                        </span>
                                        <span
                                            class="text-xs font-bold text-foreground"
                                        >
                                            {{ invoice.client.company_name }}
                                        </span>
                                    </div>
                                </div>

                                <div
                                    v-else
                                    class="pt-2 text-[10px] font-bold tracking-widest text-muted-foreground/40 uppercase italic"
                                >
                                    {{ t('invoices.show.individualClient') }}
                                </div>
                            </CardContent>
                        </Card>

                        <Card
                            class="group overflow-hidden rounded-3xl border-none bg-background shadow-sm"
                        >
                            <CardContent class="space-y-5 p-6">
                                <div class="mb-1 flex items-center gap-2">
                                    <History
                                        class="h-3.5 w-3.5 text-muted-foreground/50"
                                    />
                                    <h3
                                        class="text-[10px] font-black tracking-[0.2em] text-muted-foreground/70 uppercase"
                                    >
                                        {{ t('invoices.show.billingTimeline') }}
                                    </h3>
                                </div>

                                <div
                                    class="group/item flex items-center justify-between"
                                >
                                    <span
                                        class="flex items-center gap-2 text-[11px] font-bold tracking-wider text-muted-foreground uppercase italic"
                                    >
                                        <Calendar
                                            class="h-3.5 w-3.5 text-primary/40 transition-colors group-hover/item:text-primary"
                                        />
                                        {{ t('invoices.show.issued') }}
                                    </span>
                                    <span
                                        class="text-sm font-black text-foreground"
                                    >
                                        {{ formatDate(invoice.issue_date) }}
                                    </span>
                                </div>

                                <div
                                    class="group/item flex items-center justify-between"
                                >
                                    <span
                                        class="flex items-center gap-2 text-[11px] font-bold tracking-wider text-muted-foreground uppercase italic"
                                    >
                                        <Clock
                                            class="h-3.5 w-3.5 text-primary/40 transition-colors group-hover/item:text-primary"
                                        />
                                        {{ t('invoices.show.dueDate') }}
                                    </span>
                                    <span
                                        :class="[
                                            'text-sm font-black transition-colors',
                                            invoice.status === 'overdue'
                                                ? 'rounded-lg bg-destructive/10 px-2 py-0.5 text-destructive'
                                                : 'text-foreground',
                                        ]"
                                    >
                                        {{ formatDate(invoice.due_date) }}
                                    </span>
                                </div>

                                <div
                                    v-if="invoice.paid_at"
                                    class="relative mt-4 border-t border-dashed border-border/60 pt-4"
                                >
                                    <div
                                        class="absolute -top-[5px] left-1/2 -translate-x-1/2 bg-background px-2"
                                    >
                                        <div
                                            class="h-2 w-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"
                                        />
                                    </div>

                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <span
                                            class="flex items-center gap-2 text-[11px] font-black tracking-widest text-emerald-600 uppercase italic"
                                        >
                                            <CheckCircle2 class="h-3.5 w-3.5" />
                                            {{ t('invoices.show.paidOn') }}
                                        </span>
                                        <span
                                            class="text-sm font-black text-emerald-600"
                                        >
                                            {{ formatDate(invoice.paid_at) }}
                                        </span>
                                    </div>
                                </div>

                                <div
                                    v-else-if="invoice.status === 'overdue'"
                                    class="pt-2 text-right"
                                >
                                    <span
                                        class="animate-pulse text-[9px] font-black tracking-tighter text-destructive uppercase"
                                    >
                                        {{ t('invoices.show.paymentDelayed') }}
                                    </span>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </div>

        <UpgradeModal v-model:open="upgradeModalOpen" />

        <Dialog v-model:open="deleteDialogOpen">
            <DialogContent class="rounded-xl">
                <DialogHeader>
                    <div
                        class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-destructive/10"
                    >
                        <Trash2 class="h-7 w-7 text-destructive" />
                    </div>
                    <DialogTitle class="text-center text-xl font-bold">
                        {{ t('invoices.show.destroyTitle') }}
                    </DialogTitle>
                    <DialogDescription class="text-center">
                        {{ t('invoices.show.destroyDesc', { number: invoice.number }) }}
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter class="flex flex-col gap-3 sm:flex-row">
                    <DialogClose as-child>
                        <Button variant="ghost" class="flex-1 rounded-xl">
                            {{ t('invoices.show.keepInvoice') }}
                        </Button>
                    </DialogClose>
                    <Button
                        variant="destructive"
                        class="flex-1 rounded-xl font-bold shadow-lg shadow-destructive/20"
                        :disabled="deleting"
                        @click="deleteInvoice"
                    >
                        {{ deleting ? t('invoices.show.deleting') : t('invoices.show.confirmDeletion') }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
