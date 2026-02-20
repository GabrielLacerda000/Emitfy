<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2, User, Building2, Mail } from 'lucide-vue-next';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
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

const { t } = useI18n();

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

type Props = { clients: PaginatedClients };
const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [{ title: t('nav.clients'), href: index().url }];

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

const thClass =
    'px-6 py-4 text-left text-[10px] font-black tracking-[0.2em] text-muted-foreground/80 uppercase';
</script>

<template>
    <Head title="Clients" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 bg-muted/5 p-6">
            <div class="flex items-end justify-between">
                <Heading
                    :title="t('clients.title')"
                    :description="t('clients.description')"
                />
                <Button
                    as-child
                    class="h-11 rounded-xl bg-primary px-6  shadow-lg shadow-primary/20 transition-all hover:-translate-y-0.5 hover:shadow-primary/30"
                >
                    <Link :href="create().url">
                        <Plus class="mr-2 h-4 w-4" />
                        {{ t('clients.newClient') }}
                    </Link>
                </Button>
            </div>

            <div
                v-if="props.clients.data.length === 0"
                class="flex flex-1 flex-col items-center justify-center rounded-2xl border border-dashed border-border/60 bg-background p-12"
            >
                <div
                    class="flex h-20 w-20 items-center justify-center rounded-full bg-muted/30"
                >
                    <User class="h-10 w-10 text-muted-foreground/40" />
                </div>
                <h3 class="mt-4 text-lg font-bold">{{ t('clients.empty.title') }}</h3>
                <p
                    class="mb-6 max-w-xs text-center text-sm text-muted-foreground"
                >
                    {{ t('clients.empty.desc') }}
                </p>
                <Button variant="outline" as-child class="rounded-xl border-2">
                    <Link :href="create().url">{{ t('clients.empty.cta') }}</Link>
                </Button>
            </div>

            <div
                v-else
                class="overflow-hidden rounded-2xl border border-border/60 bg-background shadow-sm transition-all"
            >
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="border-b border-border/60 bg-muted/30">
                                <th :class="thClass">{{ t('clients.table.client') }}</th>
                                <th :class="thClass">{{ t('clients.table.company') }}</th>
                                <th :class="thClass">{{ t('clients.table.contact') }}</th>
                                <th
                                    class="px-6 py-4 text-right text-[10px] font-black tracking-[0.2em] text-muted-foreground/80 uppercase"
                                >
                                    {{ t('clients.table.actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border/40">
                            <tr
                                v-for="client in props.clients.data"
                                :key="client.id"
                                class="group cursor-pointer transition-colors hover:bg-muted/20"
                                @click="
                                    router.visit(
                                        show({ client: client.id }).url,
                                    )
                                "
                            >
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex h-9 w-9 items-center justify-center rounded-lg border border-primary/10 bg-primary/5 text-xs font-bold text-primary transition-colors group-hover:bg-primary group-hover:text-white"
                                        >
                                            {{ client.name.charAt(0) }}
                                        </div>
                                        <span
                                            class="text-sm font-bold tracking-tight"
                                            >{{ client.name }}</span
                                        >
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div
                                        class="flex items-center gap-2 text-sm font-medium text-muted-foreground"
                                    >
                                        <Building2
                                            class="h-3.5 w-3.5 opacity-50"
                                        />
                                        {{ client.company_name || t('clients.personal') }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-sm">
                                    <div
                                        class="flex items-center gap-2 text-muted-foreground"
                                    >
                                        <Mail class="h-3.5 w-3.5 opacity-50" />
                                        {{ client.email }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-right" @click.stop>
                                    <div
                                        class="flex items-center justify-end gap-1"
                                    >
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            class="h-8 w-8 rounded-lg hover:bg-background hover:shadow-sm"
                                            as-child
                                        >
                                            <Link
                                                :href="
                                                    edit({ client: client.id })
                                                        .url
                                                "
                                            >
                                                <Pencil class="h-3.5 w-3.5" />
                                            </Link>
                                        </Button>

                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            class="h-8 w-8 rounded-lg text-destructive hover:bg-destructive/5 hover:text-destructive"
                                            @click="confirmDelete(client)"
                                        >
                                            <Trash2 class="h-3.5 w-3.5" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    v-if="props.clients.last_page > 1"
                    class="flex items-center justify-between border-t border-border/60 bg-muted/10 px-6 py-4"
                >
                    <span
                        class="text-xs font-bold tracking-widest text-muted-foreground/60 uppercase"
                    >
                        {{ t('clients.pagination.showing', { count: props.clients.data.length, total: props.clients.total }) }}
                    </span>
                    <div class="flex gap-2">
                        <template
                            v-for="link in props.clients.links"
                            :key="link.label"
                        >
                            <Button
                                v-if="link.url"
                                variant="ghost"
                                size="sm"
                                :class="[
                                    'h-8 rounded-lg px-3 text-xs font-bold transition-all',
                                    link.active
                                        ? 'border border-border/60 bg-background text-primary shadow-sm'
                                        : 'text-muted-foreground',
                                ]"
                                as-child
                            >
                                <Link :href="link.url"
                                    ><span v-html="link.label"
                                /></Link>
                            </Button>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <Dialog v-model:open="deleteDialogOpen">
            <DialogContent class="max-w-[400px] rounded-2xl">
                <DialogHeader>
                    <div
                        class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-destructive/10"
                    >
                        <Trash2 class="h-6 w-6 text-destructive" />
                    </div>
                    <DialogTitle class="text-center text-xl font-bold">
                        {{ t('clients.delete.title') }}
                    </DialogTitle>
                    <DialogDescription class="pt-2 text-center">
                        {{ t('clients.delete.desc', { name: clientToDelete?.name }) }}
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter class="mt-6 flex flex-col gap-3 sm:flex-row">
                    <DialogClose as-child>
                        <Button variant="ghost" class="flex-1 rounded-xl">
                            {{ t('clients.delete.cancel') }}
                        </Button>
                    </DialogClose>

                    <Button
                        variant="destructive"
                        class="flex-1 rounded-xl font-bold"
                        :disabled="deleting"
                        @click="deleteClient"
                    >
                        {{ deleting ? t('clients.delete.deleting') : t('clients.delete.confirm') }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
