<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Pencil, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';
import ClientController from '@/actions/App/Http/Controllers/ClientController';
import ClientInfoCard from '@/components/clients/ClientInfoCard.vue';
import ClientInvoicesTable from '@/components/clients/ClientInvoicesTable.vue';
import ClientStatsCards from '@/components/clients/ClientStatsCards.vue';
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
import type {
    BreadcrumbItem,
    Client,
    ClientStats,
    PaginatedInvoices,
} from '@/types';

type Props = {
    client: Client;
    stats: ClientStats;
    invoices: PaginatedInvoices;
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Clients',
        href: index().url,
    },
    {
        title: props.client.name,
        href: '#',
    },
];

const deleteDialogOpen = ref(false);
const deleting = ref(false);

function confirmDelete() {
    deleteDialogOpen.value = true;
}

function deleteClient() {
    deleting.value = true;
    router.delete(ClientController.destroy.url({ client: props.client.id }), {
        onFinish: () => {
            deleting.value = false;
        },
    });
}
</script>

<template>
    <Head :title="client.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="mx-auto w-full max-w-6xl">
                <!-- Header -->
                <div class="flex items-start justify-between">
                    <div>
                        <h1 class="text-2xl font-bold">{{ client.name }}</h1>
                        <p class="mt-1 text-sm text-muted-foreground">
                            {{ client.email }}
                        </p>
                    </div>

                    <div class="flex gap-2">
                        <Button variant="outline" as-child>
                            <Link :href="edit({ client: client.id }).url">
                                <Pencil class="mr-2 h-4 w-4" />
                                Edit
                            </Link>
                        </Button>
                        <Button variant="destructive" @click="confirmDelete">
                            <Trash2 class="mr-2 h-4 w-4" />
                            Delete
                        </Button>
                    </div>
                </div>

                <!-- Client Info Card -->
                <ClientInfoCard
                    :client="client"
                    :last-invoice-sent="stats.lastInvoiceSent"
                    class="mt-6"
                />

                <!-- Stats Cards -->
                <ClientStatsCards :stats="stats" class="mt-6" />

                <!-- Invoices Table -->
                <ClientInvoicesTable :invoices="invoices" class="mt-6" />
            </div>
        </div>

        <!-- Delete Confirmation Dialog -->
        <Dialog v-model:open="deleteDialogOpen">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Delete Client</DialogTitle>
                    <DialogDescription>
                        Are you sure you want to delete
                        <strong>{{ client.name }}</strong
                        >? This action cannot be undone and will also delete all
                        associated invoices.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter class="gap-2">
                    <DialogClose as-child>
                        <Button variant="secondary">Cancel</Button>
                    </DialogClose>
                    <Button
                        variant="destructive"
                        :disabled="deleting"
                        @click="deleteClient"
                    >
                        Delete
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
