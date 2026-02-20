<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Pencil, Trash2, Mail, Building2, Calendar } from 'lucide-vue-next';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import ClientController from '@/actions/App/Http/Controllers/ClientController';
import ClientInfoCard from '@/components/clients/ClientInfoCard.vue';
import ClientInvoicesTable from '@/components/clients/ClientInvoicesTable.vue';
import ClientStatsCards from '@/components/clients/ClientStatsCards.vue';
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
import { edit, index } from '@/routes/clients';
import { create as createInvoice } from '@/routes/invoices';
import type {
    BreadcrumbItem,
    Client,
    ClientStats,
    PaginatedInvoices,
} from '@/types';

const { t } = useI18n();

const props = defineProps<{
    client: Client;
    stats: ClientStats;
    invoices: PaginatedInvoices;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: t('nav.clients'), href: index().url },
    { title: props.client.name, href: '#' },
];

const deleteDialogOpen = ref(false);
const deleting = ref(false);

const getInitials = (name: string) =>
    name
        .split(' ')
        .map((n) => n[0])
        .join('')
        .toUpperCase()
        .substring(0, 2);

function deleteClient() {
    deleting.value = true;
    router.delete(ClientController.destroy.url({ client: props.client.id }), {
        onFinish: () => (deleting.value = false),
    });
}

function redirectToCreateInvoice() {
    router.visit(createInvoice().url);
}
</script>

<template>
    <Head :title="client.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-8 p-6 lg:p-8">
            <div class="mx-auto w-full max-w-7xl">
                <div
                    class="mb-8 flex flex-col justify-between gap-6 md:flex-row md:items-end"
                >
                    <div class="flex items-start gap-5">
                        <div
                            class="flex h-20 w-20 shrink-0 items-center justify-center rounded-3xl bg-primary/10 text-2xl font-black text-primary shadow-inner"
                        >
                            {{ getInitials(client.name) }}
                        </div>

                        <div class="space-y-1">
                            <div class="flex items-center gap-3">
                                <h1
                                    class="text-3xl font-black tracking-tight text-foreground"
                                >
                                    {{ client.name }}
                                </h1>
                                <Badge
                                    variant="outline"
                                    class="border-emerald-500/20 bg-emerald-500/5 font-bold text-emerald-600"
                                >
                                    {{ t('clients.show.activeClient') }}
                                </Badge>
                            </div>

                            <div
                                class="flex flex-wrap items-center gap-x-4 gap-y-1 text-muted-foreground"
                            >
                                <span
                                    class="flex items-center gap-1.5 text-sm font-medium"
                                >
                                    <Mail class="h-4 w-4 opacity-70" />
                                    {{ client.email }}
                                </span>
                                <span
                                    v-if="client.company_name"
                                    class="flex items-center gap-1.5 text-sm font-medium"
                                >
                                    <Building2 class="h-4 w-4 opacity-70" />
                                    {{ client.company_name }}
                                </span>
                                <span
                                    class="flex items-center gap-1.5 text-sm font-medium"
                                >
                                    <Calendar class="h-4 w-4 opacity-70" />
                                    {{ t('clients.show.joined') }}
                                    {{
                                        new Date(
                                            client.created_at,
                                        ).toLocaleDateString()
                                    }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <Button
                            variant="outline"
                            class="shadow-sm hover:bg-muted"
                            as-child
                        >
                            <Link :href="edit({ client: client.id }).url">
                                <Pencil class="mr-2 h-4 w-4" />
                                {{ t('clients.show.editDetails') }}
                            </Link>
                        </Button>
                        <Button
                            variant="ghost"
                            class="text-destructive hover:bg-destructive/10 hover:text-destructive"
                            @click="deleteDialogOpen = true"
                        >
                            <Trash2 class="mr-2 h-4 w-4" />
                            {{ t('clients.show.delete') }}
                        </Button>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                    <div class="space-y-8 lg:col-span-2">
                        <section>
                            <div class="mb-4 flex items-center justify-between">
                                <h2
                                    class="text-sm font-bold tracking-widest text-muted-foreground uppercase"
                                >
                                    {{ t('clients.show.financialOverview') }}
                                </h2>
                            </div>
                            <ClientStatsCards :stats="stats" />
                        </section>

                        <section
                            class="rounded-3xl border border-border/50 bg-card p-2 shadow-sm"
                        >
                            <div class="flex items-center justify-between p-4">
                                <h2 class="font-bold tracking-tight">
                                    {{ t('clients.show.recentInvoices') }}
                                </h2>
                                <Button
                                    size="sm"
                                    variant="secondary"
                                    class="font-bold"
                                    @click="redirectToCreateInvoice"
                                    >{{ t('clients.show.newInvoice') }}</Button
                                >
                            </div>
                            <ClientInvoicesTable :invoices="invoices" />
                        </section>
                    </div>

                    <div class="space-y-8">
                        <section>
                            <h2
                                class="mb-4 text-sm font-bold tracking-widest text-muted-foreground uppercase"
                            >
                                {{ t('clients.show.clientDetails') }}
                            </h2>
                            <ClientInfoCard
                                :client="client"
                                :last-invoice-sent="stats.lastInvoiceSent"
                                class="border-primary/10 shadow-md"
                            />
                        </section>
                    </div>
                </div>
            </div>
        </div>

        <Dialog v-model:open="deleteDialogOpen">
            <DialogContent class="rounded-xl sm:max-w-[425px]">
                <DialogHeader>
                    <div
                        class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-destructive/10"
                    >
                        <Trash2 class="h-6 w-6 text-destructive" />
                    </div>
                    <DialogTitle class="text-center text-xl font-bold">{{
                        t('clients.show.deleteTitle')
                    }}</DialogTitle>
                    <DialogDescription class="text-center">
                        {{
                            t('clients.show.deleteDesc', {
                                name: client.name,
                                count: invoices.total,
                            })
                        }}
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter class="mt-6 flex flex-col gap-3 sm:flex-row">
                    <DialogClose as-child>
                        <Button variant="ghost" class="flex-1 rounded-xl">{{
                            t('clients.show.goBack')
                        }}</Button>
                    </DialogClose>
                    <Button
                        variant="destructive"
                        class="flex-1 rounded-xl font-bold"
                        :disabled="deleting"
                        @click="deleteClient"
                    >
                        {{
                            deleting
                                ? t('clients.show.deleting')
                                : t('clients.show.confirmDelete')
                        }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
