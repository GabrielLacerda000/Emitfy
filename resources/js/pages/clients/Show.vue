<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Pencil, Trash2, Mail, Building2, Calendar } from 'lucide-vue-next';
import { ref } from 'vue';
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
import type { BreadcrumbItem, Client, ClientStats, PaginatedInvoices } from '@/types';

const props = defineProps<{
    client: Client;
    stats: ClientStats;
    invoices: PaginatedInvoices;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Clients', href: index().url },
    { title: props.client.name, href: '#' },
];

const deleteDialogOpen = ref(false);
const deleting = ref(false);

const getInitials = (name: string) => name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);

function deleteClient() {
    deleting.value = true;
    router.delete(ClientController.destroy.url({ client: props.client.id }), {
        onFinish: () => deleting.value = false,
    });
}
</script>

<template>
    <Head :title="client.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-8 p-6 lg:p-8">
            <div class="mx-auto w-full max-w-7xl">
                
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
                    <div class="flex items-start gap-5">
                        <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-3xl bg-primary/10 text-primary text-2xl font-black shadow-inner">
                            {{ getInitials(client.name) }}
                        </div>
                        
                        <div class="space-y-1">
                            <div class="flex items-center gap-3">
                                <h1 class="text-3xl font-black tracking-tight text-foreground">{{ client.name }}</h1>
                                <Badge variant="outline" class="bg-emerald-500/5 text-emerald-600 border-emerald-500/20 font-bold">
                                    Active Client
                                </Badge>
                            </div>
                            
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-muted-foreground">
                                <span class="flex items-center gap-1.5 text-sm font-medium">
                                    <Mail class="h-4 w-4 opacity-70" /> {{ client.email }}
                                </span>
                                <span v-if="client.company_name" class="flex items-center gap-1.5 text-sm font-medium">
                                    <Building2 class="h-4 w-4 opacity-70" /> {{ client.company_name }}
                                </span>
                                <span class="flex items-center gap-1.5 text-sm font-medium">
                                    <Calendar class="h-4 w-4 opacity-70" /> Joined {{ new Date(client.created_at).toLocaleDateString() }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <Button variant="outline" class="shadow-sm hover:bg-muted" as-child>
                            <Link :href="edit({ client: client.id }).url">
                                <Pencil class="mr-2 h-4 w-4" />
                                Edit Details
                            </Link>
                        </Button>
                        <Button variant="ghost" class="text-destructive hover:bg-destructive/10 hover:text-destructive" @click="deleteDialogOpen = true">
                            <Trash2 class="mr-2 h-4 w-4" />
                            Delete
                        </Button>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 space-y-8">
                        <section>
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-sm font-bold uppercase tracking-widest text-muted-foreground">Financial Overview</h2>
                            </div>
                            <ClientStatsCards :stats="stats" />
                        </section>

                        <section class="rounded-3xl border border-border/50 bg-card p-2 shadow-sm">
                            <div class="p-4 flex items-center justify-between">
                                <h2 class="font-bold tracking-tight">Recent Invoices</h2>
                                <Button size="sm" variant="secondary" class="font-bold">New Invoice +</Button>
                            </div>
                            <ClientInvoicesTable :invoices="invoices" />
                        </section>
                    </div>

                    <div class="space-y-8">
                        <section>
                            <h2 class="text-sm font-bold uppercase tracking-widest text-muted-foreground mb-4">Client Details</h2>
                            <ClientInfoCard
                                :client="client"
                                :last-invoice-sent="stats.lastInvoiceSent"
                                class="shadow-md border-primary/10"
                            />
                        </section>
                        <!-- TODO: Add quick support section BACK END -->
                        <!-- <div class="rounded-2xl bg-primary p-6 text-primary-foreground shadow-lg shadow-primary/20">
                            <h3 class="font-bold text-lg mb-2">Quick Support</h3>
                            <p class="text-sm opacity-90 mb-4">Need to export all data for this specific client?</p>
                            <Button variant="secondary" class="w-full font-bold">Export Report (CSV)</Button>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>

        <Dialog v-model:open="deleteDialogOpen">
            <DialogContent class="sm:max-w-[425px] rounded-xl">
                <DialogHeader>
                    <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-destructive/10">
                        <Trash2 class="h-6 w-6 text-destructive" />
                    </div>
                    <DialogTitle class="text-center text-xl font-bold">Delete Client</DialogTitle>
                    <DialogDescription class="text-center">
                        Are you sure you want to delete <span class="font-bold text-foreground">{{ client.name }}</span>? 
                        This will permanently remove all <span class="text-destructive font-bold">{{ invoices.total }} invoices</span>.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter class="mt-6 flex flex-col sm:flex-row gap-3">
                    <DialogClose as-child>
                        <Button variant="ghost" class="flex-1 rounded-xl">Go Back</Button>
                    </DialogClose>
                    <Button
                        variant="destructive"
                        class="flex-1 rounded-xl font-bold"
                        :disabled="deleting"
                        @click="deleteClient"
                    >
                        {{ deleting ? 'Deleting...' : 'Yes, Delete Client' }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>