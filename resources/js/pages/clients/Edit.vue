<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import {
    User,
    Mail,
    Building2,
    FileText,
    ArrowLeft,
    LoaderCircle,
} from 'lucide-vue-next'; // Ícones para consistência
import ClientController from '@/actions/App/Http/Controllers/ClientController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card'; // Importante adicionar o Card
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
    { title: 'Clients', href: index().url },
    { title: 'Edit Client', href: edit({ client: props.client.id }).url },
];
</script>

<template>
    <Head :title="`Edit ${client.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-8 bg-muted/20 p-6">
            <div class="mx-auto w-full max-w-2xl">
                <div
                    class="mb-8 flex flex-col justify-between gap-4 md:flex-row md:items-center"
                >
                    <Heading
                        title="Edit Client"
                        :description="`Update information for ${client.name}`"
                    />
                    <Button
                        variant="ghost"
                        class="group self-start rounded-xl md:self-auto"
                        as-child
                    >
                        <Link :href="index().url">
                            <ArrowLeft
                                class="mr-2 h-4 w-4 transition-transform group-hover:-translate-x-1"
                            />
                            Back to list
                        </Link>
                    </Button>
                </div>

                <Form
                    v-bind="ClientController.update.form({ client: client.id })"
                    v-slot="{ errors, processing, recentlySuccessful }"
                    class="space-y-8"
                >
                    <Card
                        class="overflow-hidden rounded-3xl border-none bg-background shadow-sm"
                    >
                        <div
                            class="border-b border-primary/5 bg-primary/5 px-6 py-4"
                        >
                            <h3
                                class="flex items-center gap-2 text-[10px] font-black tracking-widest text-primary/70 uppercase"
                            >
                                <span class="rounded-md bg-primary/10 p-1"
                                    ><User class="h-3 w-3"
                                /></span>
                                Personal & Contact Info
                            </h3>
                        </div>

                        <div class="space-y-6 p-6">
                            <div class="grid gap-2">
                                <Label
                                    for="name"
                                    class="ml-1 text-[10px] font-black tracking-widest text-muted-foreground/80 uppercase"
                                    >Full Name</Label
                                >
                                <div class="group relative">
                                    <User
                                        class="absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 text-muted-foreground/40 transition-colors group-focus-within:text-primary"
                                    />
                                    <Input
                                        id="name"
                                        name="name"
                                        :default-value="client.name"
                                        required
                                        class="h-12 rounded-xl border-border/60 bg-muted/20 pl-11 transition-all focus:bg-background"
                                        placeholder="John Doe"
                                    />
                                </div>
                                <InputError :message="errors.name" />
                            </div>

                            <div class="grid gap-2">
                                <Label
                                    for="email"
                                    class="ml-1 text-[10px] font-black tracking-widest text-muted-foreground/80 uppercase"
                                    >Email Address</Label
                                >
                                <div class="group relative">
                                    <Mail
                                        class="absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 text-muted-foreground/40 transition-colors group-focus-within:text-primary"
                                    />
                                    <Input
                                        id="email"
                                        type="email"
                                        name="email"
                                        :default-value="client.email"
                                        required
                                        class="h-12 rounded-xl border-border/60 bg-muted/20 pl-11 transition-all focus:bg-background"
                                        placeholder="client@company.com"
                                    />
                                </div>
                                <InputError :message="errors.email" />
                            </div>

                            <div class="grid gap-2">
                                <Label
                                    for="company_name"
                                    class="ml-1 text-[10px] font-black tracking-widest text-muted-foreground/80 uppercase"
                                    >Company (Optional)</Label
                                >
                                <div class="group relative">
                                    <Building2
                                        class="absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 text-muted-foreground/40 transition-colors group-focus-within:text-primary"
                                    />
                                    <Input
                                        id="company_name"
                                        name="company_name"
                                        :default-value="
                                            client.company_name ?? ''
                                        "
                                        class="h-12 rounded-xl border-border/60 bg-muted/20 pl-11 transition-all focus:bg-background"
                                        placeholder="Acme Inc."
                                    />
                                </div>
                                <InputError :message="errors.company_name" />
                            </div>
                        </div>
                    </Card>

                    <Card
                        class="overflow-hidden rounded-3xl border-none bg-background shadow-sm"
                    >
                        <div
                            class="border-b border-primary/5 bg-primary/5 px-6 py-4"
                        >
                            <h3
                                class="flex items-center gap-2 text-[10px] font-black tracking-widest text-primary/70 uppercase"
                            >
                                <span class="rounded-md bg-primary/10 p-1"
                                    ><FileText class="h-3 w-3"
                                /></span>
                                Additional Notes
                            </h3>
                        </div>
                        <div class="p-6">
                            <textarea
                                id="notes"
                                name="notes"
                                rows="4"
                                :value="client.notes ?? ''"
                                placeholder="Describe any relevant details..."
                                class="w-full resize-none rounded-2xl border-border/60 bg-muted/20 p-4 text-sm transition-all outline-none focus:bg-background focus:ring-2 focus:ring-primary/20"
                            />
                            <InputError :message="errors.notes" class="mt-2" />
                        </div>
                    </Card>

                    <div class="flex items-center justify-end gap-4 pt-2">
                        <Transition
                            enter-active-class="transition ease-out duration-200"
                            enter-from-class="opacity-0 translate-y-1"
                            enter-to-class="opacity-100 translate-y-0"
                        >
                            <p
                                v-if="recentlySuccessful"
                                class="flex items-center gap-2 text-sm font-bold text-emerald-500"
                            >
                                <span
                                    class="h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-500"
                                />
                                Changes saved!
                            </p>
                        </Transition>

                        <Button
                            type="submit"
                            :disabled="processing"
                            class="h-12 rounded-xl bg-primary px-8 font-black shadow-lg shadow-primary/20 transition-all hover:-translate-y-0.5 hover:shadow-primary/30"
                        >
                            <LoaderCircle v-if="processing" class="mr-2" />
                            Save Client Info
                        </Button>
                    </div>
                </Form>
            </div>
        </div>
    </AppLayout>
</template>
