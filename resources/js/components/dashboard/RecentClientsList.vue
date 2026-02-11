<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowRight, Plus, User, Users } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { show } from '@/routes/clients';
import type { Client } from '@/types';

type Props = {
    clients: (Client & { invoices_count: number })[];
    viewAllUrl: string;
    manageClientsUrl: string;
};

defineProps<Props>();
</script>

<template>
    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold">Recent Clients</h3>
            <Button variant="ghost" size="sm" as-child>
                <Link :href="viewAllUrl">
                    Manage Clients
                    <ArrowRight class="ml-2 h-4 w-4" />
                </Link>
            </Button>
        </div>

        <!-- Clients list or Empty State -->
        <div v-if="clients.length > 0" class="flex flex-col gap-2">
            <Link
                v-for="client in clients"
                :key="client.id"
                :href="show({ client: client.id }).url"
                class="group flex items-center justify-between rounded-xl border border-sidebar-border/70 p-4 transition-colors hover:bg-muted/50 dark:border-sidebar-border cursor-pointer"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10"
                    >
                        <User class="h-5 w-5 text-primary" />
                    </div>

                    <div>
                        <div class="font-medium">{{ client.name }}</div>
                        <div class="text-sm text-muted-foreground">
                            {{ client.company_name || client.email }}
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 text-sm text-muted-foreground">
                    <span>{{ client.invoices_count }} invoices</span>
                    <ArrowRight
                        class="h-4 w-4 transition-transform group-hover:translate-x-1"
                    />
                </div>
            </Link>
        </div>

        <!-- Empty state -->
        <div
            v-else
            class="flex flex-col items-center justify-center rounded-xl border border-dashed border-sidebar-border/70 p-8 text-center dark:border-sidebar-border"
        >
            <Users class="h-12 w-12 text-muted-foreground" />
            <h3 class="mt-4 text-lg font-medium">No clients yet</h3>
            <p class="mt-1 text-sm text-muted-foreground">
                Add your first client to get started
            </p>
            <Button class="mt-4" as-child>
                <Link :href="manageClientsUrl">
                    <Plus class="mr-2 h-4 w-4" />
                    Add Client
                </Link>
            </Button>
        </div>
    </div>
</template>
