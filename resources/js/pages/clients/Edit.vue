<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import ClientController from '@/actions/App/Http/Controllers/ClientController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { index, edit } from '@/routes/clients';
import { type BreadcrumbItem, type Client } from '@/types';

type Props = {
    client: Client;
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Clients',
        href: index().url,
    },
    {
        title: 'Edit Client',
        href: edit({ client: props.client.id }).url,
    },
];
</script>

<template>
    <Head :title="`Edit ${client.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="mx-auto w-full max-w-2xl">
                <Heading
                    title="Edit Client"
                    :description="`Update information for ${client.name}`"
                />

                <Form
                    v-bind="ClientController.update.form({ client: client.id })"
                    class="mt-6 space-y-6"
                    v-slot="{ errors, processing, recentlySuccessful }"
                >
                    <div class="grid gap-2">
                        <Label for="name">Name</Label>
                        <Input
                            id="name"
                            name="name"
                            :default-value="client.name"
                            required
                            autocomplete="name"
                            placeholder="Client name"
                        />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email</Label>
                        <Input
                            id="email"
                            type="email"
                            name="email"
                            :default-value="client.email"
                            required
                            autocomplete="email"
                            placeholder="client@example.com"
                        />
                        <InputError :message="errors.email" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="company_name">Company Name</Label>
                        <Input
                            id="company_name"
                            name="company_name"
                            :default-value="client.company_name ?? ''"
                            autocomplete="organization"
                            placeholder="Company name (optional)"
                        />
                        <InputError :message="errors.company_name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="notes">Notes</Label>
                        <textarea
                            id="notes"
                            name="notes"
                            rows="4"
                            :value="client.notes ?? ''"
                            placeholder="Additional notes about this client (optional)"
                            class="placeholder:text-muted-foreground dark:bg-input/30 border-input w-full min-w-0 rounded-md border bg-transparent px-3 py-2 text-base shadow-xs transition-[color,box-shadow] outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                        />
                        <InputError :message="errors.notes" />
                    </div>

                    <div class="flex items-center gap-4">
                        <Button :disabled="processing">Update Client</Button>
                        <Button variant="ghost" as-child>
                            <Link :href="index().url">Cancel</Link>
                        </Button>
                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p
                                v-show="recentlySuccessful"
                                class="text-sm text-neutral-600"
                            >
                                Saved.
                            </p>
                        </Transition>
                    </div>
                </Form>
            </div>
        </div>
    </AppLayout>
</template>
