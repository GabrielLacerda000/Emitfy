<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';
import ClientController from '@/actions/App/Http/Controllers/ClientController';
import Heading from '@/components/Heading.vue';
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
import { index, create, edit, show } from '@/routes/clients';
import { type BreadcrumbItem, type Client } from '@/types';

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedClients {
    data: Client[];
    links: PaginationLink[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

type Props = {
    clients: PaginatedClients;
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Clients',
        href: index().url,
    },
];

const deleteDialogOpen = ref(false);
const clientToDelete = ref<Client | null>(null);
const deleting = ref(false);

function confirmDelete(client: Client) {
    clientToDelete.value = client;
    deleteDialogOpen.value = true;
}

function deleteClient() {
    if (!clientToDelete.value) return;

    deleting.value = true;
    router.delete(
        ClientController.destroy.url({ client: clientToDelete.value.id }),
        {
            preserveScroll: true,
            onFinish: () => {
                deleting.value = false;
                deleteDialogOpen.value = false;
                clientToDelete.value = null;
            },
        },
    );
}
</script>

<template>
    <Head title="Clients" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <Heading
                    title="Clients"
                    description="Manage your clients and their information"
                />
                <Button as-child>
                    <Link :href="create().url">
                        <Plus class="mr-2 h-4 w-4" />
                        New Client
                    </Link>
                </Button>
            </div>

            <!-- Empty State -->
            <div
                v-if="props.clients.data.length === 0"
                class="flex flex-1 flex-col items-center justify-center rounded-xl border border-dashed border-sidebar-border/70 p-8 dark:border-sidebar-border"
            >
                <div class="text-center">
                    <h3 class="text-lg font-medium">No clients yet</h3>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Get started by creating your first client.
                    </p>
                    <Button class="mt-4" as-child>
                        <Link :href="create().url">
                            <Plus class="mr-2 h-4 w-4" />
                            New Client
                        </Link>
                    </Button>
                </div>
            </div>

            <!-- Clients Table -->
            <div
                v-else
                class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
            >
                <table class="w-full">
                    <thead>
                        <tr
                            class="border-b border-sidebar-border/70 dark:border-sidebar-border"
                        >
                            <th
                                class="px-4 py-3 text-left text-sm font-medium text-muted-foreground"
                            >
                                Name
                            </th>
                            <th
                                class="px-4 py-3 text-left text-sm font-medium text-muted-foreground"
                            >
                                Email
                            </th>
                            <th
                                class="px-4 py-3 text-left text-sm font-medium text-muted-foreground"
                            >
                                Company
                            </th>
                            <th
                                class="px-4 py-3 text-right text-sm font-medium text-muted-foreground"
                            >
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="client in props.clients.data"
                            :key="client.id"
                            class="border-b border-sidebar-border/70 last:border-b-0 dark:border-sidebar-border hover:bg-muted/50 cursor-pointer"
                            @click="router.visit(show({ client: client.id }).url)"
                        >
                            <td class="px-4 py-3 text-sm font-medium">
                                {{ client.name }}
                            </td>
                            <td class="px-4 py-3 text-sm text-muted-foreground">
                                {{ client.email }}
                            </td>
                            <td class="px-4 py-3 text-sm text-muted-foreground">
                                {{ client.company_name || '—' }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div
                                    class="flex items-center justify-end gap-2"
                                >
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        as-child
                                        @click.stop
                                    >
                                        <Link
                                            :href="
                                                edit({ client: client.id }).url
                                            "
                                        >
                                            <Pencil class="h-4 w-4" />
                                            <span class="sr-only">Edit</span>
                                        </Link>
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        @click.stop="confirmDelete(client)"
                                    >
                                        <Trash2
                                            class="h-4 w-4 text-destructive"
                                        />
                                        <span class="sr-only">Delete</span>
                                    </Button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div
                    v-if="props.clients.last_page > 1"
                    class="flex items-center justify-between border-t border-sidebar-border/70 px-4 py-3 dark:border-sidebar-border"
                >
                    <p class="text-sm text-muted-foreground">
                        Page {{ props.clients.current_page }} of
                        {{ props.clients.last_page }} ({{ props.clients.total }}
                        total)
                    </p>
                    <div class="flex gap-1">
                        <template
                            v-for="link in props.clients.links"
                            :key="link.label"
                        >
                            <Button
                                v-if="link.url"
                                variant="outline"
                                size="sm"
                                :class="{ 'bg-accent': link.active }"
                                as-child
                            >
                                <Link :href="link.url">
                                    <span v-html="link.label" />
                                </Link>
                            </Button>

                            <Button v-else variant="outline" size="sm" disabled>
                                <span v-html="link.label" />
                            </Button>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Dialog -->
        <Dialog v-model:open="deleteDialogOpen">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Delete Client</DialogTitle>
                    <DialogDescription>
                        Are you sure you want to delete
                        <strong>{{ clientToDelete?.name }}</strong
                        >? This action cannot be undone.
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
