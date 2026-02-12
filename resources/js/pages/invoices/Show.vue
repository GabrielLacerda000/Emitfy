<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { 
    Download, Mail, Pencil, Trash2, Eye, 
    Calendar, User, FileText, CheckCircle2, 
    AlertCircle, Clock, ArrowLeft 
} from 'lucide-vue-next';
import { ref } from 'vue';
import InvoiceController from '@/actions/App/Http/Controllers/InvoiceController';
import InvoicePdfController from '@/actions/App/Http/Controllers/InvoicePdfController';
import InvoiceItemsTable from '@/components/invoices/InvoiceItemsTable.vue';
import InvoiceSummary from '@/components/invoices/InvoiceSummary.vue';
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
import { formatDate } from '@/lib/utils';
import { edit, index } from '@/routes/invoices';
import { type BreadcrumbItem, type Invoice } from '@/types';

type Props = {
    invoice: Invoice;
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Invoices', href: index().url },
    { title: props.invoice.number, href: '#' },
];

function getStatusConfig(status: string) {
    const configs = {
        draft: { variant: 'secondary' as const, label: 'Draft', icon: FileText, class: 'bg-slate-100 text-slate-600 border-slate-200' },
        sent: { variant: 'default' as const, label: 'Sent', icon: Clock, class: 'bg-blue-50 text-blue-600 border-blue-200' },
        paid: { variant: 'outline' as const, label: 'Paid', icon: CheckCircle2, class: 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10' },
        overdue: { variant: 'destructive' as const, label: 'Overdue', icon: AlertCircle, class: 'animate-pulse' },
    };
    return configs[status as keyof typeof configs] || configs.draft;
}

const deleteDialogOpen = ref(false);
const deleting = ref(false);
const downloadingPdf = ref(false);
const sending = ref(false);

function deleteInvoice() {
    deleting.value = true;
    router.delete(InvoiceController.destroy.url({ invoice: props.invoice.id }), {
        onFinish: () => deleting.value = false,
    });
}

function downloadPdf() {
    downloadingPdf.value = true;
    window.location.href = InvoicePdfController.show.url({ invoice: props.invoice.id });
    setTimeout(() => downloadingPdf.value = false, 2000);
}

function viewPdf() {
    const url = `${InvoicePdfController.show.url({ invoice: props.invoice.id })}?mode=stream`;
    window.open(url, '_blank');
}

function sendInvoice() {
    sending.value = true;
    router.post(InvoiceController.send.url({ invoice: props.invoice.id }), {}, {
        onFinish: () => sending.value = false,
    });
}
</script>

<template>
    <Head :title="`Invoice ${invoice.number}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-6 lg:p-8 bg-muted/20 min-h-screen">
            <div class="mx-auto w-full max-w-5xl">
                
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                    <div class="flex items-center gap-4">
                        <Link :href="index().url" class="p-2 hover:bg-background rounded-full transition-colors border border-transparent hover:border-border">
                            <ArrowLeft class="h-5 w-5" />
                        </Link>
                        <div>
                            <div class="flex items-center gap-3">
                                <h1 class="text-2xl font-black tracking-tight uppercase italic">{{ invoice.number }}</h1>
                                <Badge :class="[getStatusConfig(invoice.status).class, 'font-bold uppercase tracking-wider text-[10px] px-2.5 py-1 flex items-center gap-1']">
                                    <component :is="getStatusConfig(invoice.status).icon" class="h-3 w-3" />
                                    {{ getStatusConfig(invoice.status).label }}
                                </Badge>
                            </div>
                            <p class="text-sm text-muted-foreground font-medium mt-0.5">
                                Created on {{ formatDate(invoice.created_at) }}
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2 items-center bg-background p-1.5 rounded-2xl border border-border/60 shadow-sm">
                        <Button variant="ghost" size="sm" class="font-bold text-xs" as-child>
                            <Link :href="edit({ invoice: invoice.id }).url">
                                <Pencil class="mr-2 h-3.5 w-3.5 text-primary" /> Edit
                            </Link>
                        </Button>
                        <div class="w-px h-4 bg-border mx-1" />
                        <Button variant="ghost" size="sm" class="font-bold text-xs" @click="viewPdf">
                            <Eye class="mr-2 h-3.5 w-3.5 text-muted-foreground" /> View PDF
                        </Button>
                        <Button variant="ghost" size="sm" class="font-bold text-xs" :disabled="downloadingPdf" @click="downloadPdf">
                            <Download class="mr-2 h-3.5 w-3.5 text-muted-foreground" /> 
                            {{ downloadingPdf ? '...' : 'Download' }}
                        </Button>
                        <Button variant="destructive" size="sm" class="h-8 rounded-xl font-bold text-xs" @click="deleteDialogOpen = true">
                            <Trash2 class="h-3.5 w-3.5" />
                        </Button>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 space-y-6">
                        <Card class="rounded-3xl border-none shadow-sm overflow-hidden">
                            <CardHeader class="border-b bg-muted/10 pb-4">
                                <CardTitle class="text-sm font-bold uppercase tracking-widest text-muted-foreground flex items-center gap-2">
                                    <FileText class="h-4 w-4" /> Services Rendered
                                </CardTitle>
                            </CardHeader>
                            <CardContent class="p-0">
                                <InvoiceItemsTable :model-value="invoice.items ?? []" readonly />
                            </CardContent>
                            <div class="p-6 bg-muted/5 border-t">
                                <InvoiceSummary :subtotal="invoice.subtotal" :tax="invoice.tax" :total="invoice.total" />
                            </div>
                        </Card>

                        <Card v-if="invoice.notes" class="rounded-3xl border-none shadow-sm">
                            <CardHeader>
                                <CardTitle class="text-sm font-bold uppercase tracking-widest text-muted-foreground">Internal Notes</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <p class="text-sm text-muted-foreground leading-relaxed italic border-l-4 border-primary/20 pl-4">
                                    "{{ invoice.notes }}"
                                </p>
                            </CardContent>
                        </Card>
                    </div>

                    <div class="space-y-6">
                        <Button 
                            class="w-full h-14 rounded-2xl font-black text-md shadow-xl shadow-primary/20 hover:shadow-primary/30 transition-all hover:-translate-y-1"
                            :disabled="sending || invoice.status === 'paid'"
                            @click="sendInvoice"
                        >
                            <Mail class="mr-3 h-5 w-5" />
                            {{ invoice.status === 'draft' ? 'Send to Client' : 'Resend Invoice' }}
                        </Button>

                        <Card class="rounded-3xl border-none shadow-sm overflow-hidden">
                            <CardHeader class="bg-primary/5 pb-4">
                                <CardTitle class="text-xs font-bold uppercase tracking-widest text-primary/70 flex items-center gap-2">
                                    <User class="h-4 w-4" /> Recipient
                                </CardTitle>
                            </CardHeader>
                            <CardContent class="pt-6">
                                <div class="font-bold text-lg leading-none">{{ invoice.client.name }}</div>
                                <div class="text-sm text-muted-foreground mt-2 font-medium">{{ invoice.client.email }}</div>
                                <div v-if="invoice.client.company_name" class="mt-4 p-3 bg-muted/40 rounded-xl text-xs font-semibold flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full bg-primary/40" />
                                    {{ invoice.client.company_name }}
                                </div>
                            </CardContent>
                        </Card>

                        <Card class="rounded-3xl border-none shadow-sm">
                            <CardContent class="pt-6 space-y-4">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-muted-foreground flex items-center gap-2 italic">
                                        <Calendar class="h-4 w-4 opacity-50" /> Issued
                                    </span>
                                    <span class="font-bold">{{ formatDate(invoice.issue_date) }}</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-muted-foreground flex items-center gap-2 italic">
                                        <Clock class="h-4 w-4 opacity-50" /> Due
                                    </span>
                                    <span :class="['font-bold', invoice.status === 'overdue' ? 'text-destructive' : '']">
                                        {{ formatDate(invoice.due_date) }}
                                    </span>
                                </div>
                                <div v-if="invoice.paid_at" class="pt-2 mt-2 border-t flex items-center justify-between text-sm text-emerald-600">
                                    <span class="flex items-center gap-2 font-medium italic">
                                        <CheckCircle2 class="h-4 w-4" /> Paid at
                                    </span>
                                    <span class="font-black">{{ formatDate(invoice.paid_at) }}</span>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </div>

        <Dialog v-model:open="deleteDialogOpen">
            <DialogContent class="rounded-xl">
                <DialogHeader>
                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-destructive/10">
                        <Trash2 class="h-7 w-7 text-destructive" />
                    </div>
                    <DialogTitle class="text-center text-xl font-bold">Destroy Invoice?</DialogTitle>
                    <DialogDescription class="text-center">
                        This will remove <span class="font-bold text-foreground">{{ invoice.number }}</span> permanently. 
                        This action is irreversible.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter class="flex flex-col sm:flex-row gap-3">
                    <DialogClose as-child>
                        <Button variant="ghost" class="flex-1 rounded-xl">Keep Invoice</Button>
                    </DialogClose>
                    <Button variant="destructive" class="flex-1 rounded-xl font-bold shadow-lg shadow-destructive/20" :disabled="deleting" @click="deleteInvoice">
                        {{ deleting ? 'Deleting...' : 'Confirm Deletion' }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>