<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowRight, Plus, Users, Mail, Building2 } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { show } from '@/routes/clients';
import type { Client } from '@/types';

const { t } = useI18n();

type Props = {
    clients: (Client & { invoices_count: number })[];
    viewAllUrl: string;
    manageClientsUrl: string;
};

defineProps<Props>();

const getInitials = (name: string) => {
    return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
};
</script>

<template>
    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between px-1">
            <div>
                <h3 class="text-lg font-bold tracking-tight text-foreground">{{ t('dashboard.recentClients.title') }}</h3>
                <p class="text-xs text-muted-foreground font-medium">{{ t('dashboard.recentClients.subtitle') }}</p>
            </div>
            <Button variant="outline" size="sm" class="h-9 rounded-lg border-border/60 hover:bg-primary/5 hover:text-primary transition-all" as-child>
                <Link :href="viewAllUrl">
                    {{ t('dashboard.recentClients.manageAll') }}
                    <ArrowRight class="ml-2 h-3.5 w-3.5" />
                </Link>
            </Button>
        </div>

        <div v-if="clients.length > 0" class="grid gap-3">
            <Link
                v-for="client in clients"
                :key="client.id"
                :href="show({ client: client.id }).url"
                class="group relative flex items-center justify-between rounded-2xl border border-border/40 bg-card p-4 transition-all duration-300 hover:border-primary/30 hover:shadow-md hover:shadow-primary/5 hover:-translate-y-0.5 cursor-pointer overflow-hidden"
            >
                <div class="absolute inset-0 bg-linear-to-r from-primary/3 to-transparent opacity-0 group-hover:opacity-100 transition-opacity" />

                <div class="relative flex items-center gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary font-bold text-sm transition-transform group-hover:scale-105">
                        {{ getInitials(client.name) }}
                    </div>

                    <div class="flex flex-col">
                        <span class="font-bold text-foreground group-hover:text-primary transition-colors">
                            {{ client.name }}
                        </span>
                        <div class="flex items-center gap-3 text-xs text-muted-foreground mt-0.5">
                            <span v-if="client.company_name" class="flex items-center gap-1">
                                <Building2 class="h-3 w-3" /> {{ client.company_name }}
                            </span>
                            <span v-else class="flex items-center gap-1">
                                <Mail class="h-3 w-3" /> {{ client.email }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="relative flex items-center gap-4">
                    <div class="hidden sm:flex flex-col items-end gap-1">
                        <Badge variant="secondary" class="bg-muted/50 font-semibold group-hover:bg-primary/10 group-hover:text-primary transition-colors">
                            {{ client.invoices_count }} {{ client.invoices_count === 1 ? t('dashboard.recentClients.invoice') : t('dashboard.recentClients.invoices') }}
                        </Badge>
                    </div>
                    <div class="h-8 w-8 flex items-center justify-center rounded-full border border-border/50 group-hover:border-primary/30 group-hover:bg-primary/5 transition-all">
                        <ArrowRight class="h-4 w-4 text-muted-foreground group-hover:text-primary transition-transform group-hover:translate-x-0.5" />
                    </div>
                </div>
            </Link>
        </div>

        <div
            v-else
            class="group flex flex-col items-center justify-center rounded-4xl border-2 border-dashed border-border/60 p-12 text-center transition-colors hover:border-primary/30 bg-muted/20"
        >
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-background shadow-sm border border-border group-hover:scale-110 transition-transform duration-500">
                <Users class="h-8 w-8 text-muted-foreground/60" />
            </div>
            <h3 class="mt-6 text-xl font-bold">{{ t('dashboard.recentClients.noClients') }}</h3>
            <p class="mt-2 text-sm text-muted-foreground max-w-[200px]">
                {{ t('dashboard.recentClients.noClientsDesc') }}
            </p>
            <Button class="mt-8 rounded-full px-8 shadow-lg shadow-primary/20 hover:shadow-primary/30 transition-all" as-child>
                <Link :href="manageClientsUrl">
                    <Plus class="mr-2 h-4 w-4" />
                    {{ t('dashboard.recentClients.addFirstClient') }}
                </Link>
            </Button>
        </div>
    </div>
</template>
