<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import {
    User,
    Mail,
    Building2,
    FileText,
    ChevronLeft,
    PlusCircle,
} from 'lucide-vue-next';
import ClientController from '@/actions/App/Http/Controllers/ClientController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { index, create } from '@/routes/clients';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Clients', href: index().url },
    { title: 'New Client', href: create().url },
];

// Estilos compartilhados para manter a consistência
const labelClass =
    'flex items-center gap-2 text-[10px] font-black tracking-[0.2em] text-muted-foreground/80 uppercase mb-2';
const cardClass =
    'rounded-2xl border border-border/60 bg-background shadow-sm overflow-hidden';
const inputClass =
    'h-11 rounded-xl border-border/40 bg-muted/20 px-4 text-sm transition-all focus:bg-background focus:ring-2 focus:ring-primary/20 outline-none';
</script>

<template>
    <Head title="New Client" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 bg-muted/5 p-6">
            <div class="mx-auto w-full max-w-2xl">
                <div class="mb-8">
                    <Heading
                        title="New Client"
                        description="Add a new business partner to your network"
                    />
                </div>

                <Form
                    v-bind="ClientController.store.form()"
                    v-slot="{ errors, processing }"
                >
                    <div class="space-y-8">
                        <div :class="cardClass">
                            <div
                                class="border-b border-border/60 bg-muted/30 px-6 py-4"
                            >
                                <h3
                                    class="flex items-center gap-2 text-sm font-bold tracking-tight"
                                >
                                    <User class="h-4 w-4 text-primary" />
                                    Identification
                                </h3>
                            </div>

                            <div class="space-y-6 p-6">
                                <div class="grid gap-2">
                                    <Label for="name" :class="labelClass"
                                        >Full Name</Label
                                    >
                                    <Input
                                        id="name"
                                        name="name"
                                        required
                                        autocomplete="name"
                                        placeholder="John Doe"
                                        :class="inputClass"
                                    />
                                    <InputError :message="errors.name" />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="email" :class="labelClass">
                                        <Mail class="h-3 w-3" /> Email Address
                                    </Label>
                                    <Input
                                        id="email"
                                        type="email"
                                        name="email"
                                        required
                                        autocomplete="email"
                                        placeholder="john@company.com"
                                        :class="inputClass"
                                    />
                                    <InputError :message="errors.email" />
                                </div>
                            </div>
                        </div>

                        <div :class="cardClass">
                            <div
                                class="border-b border-border/60 bg-muted/30 px-6 py-4"
                            >
                                <h3
                                    class="flex items-center gap-2 text-sm font-bold tracking-tight"
                                >
                                    <Building2 class="h-4 w-4 text-primary" />
                                    Professional Details
                                </h3>
                            </div>

                            <div class="space-y-6 p-6">
                                <div class="grid gap-2">
                                    <Label
                                        for="company_name"
                                        :class="labelClass"
                                        >Company Name</Label
                                    >
                                    <Input
                                        id="company_name"
                                        name="company_name"
                                        autocomplete="organization"
                                        placeholder="Acme Corp LLC"
                                        :class="inputClass"
                                    />
                                    <InputError
                                        :message="errors.company_name"
                                    />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="notes" :class="labelClass">
                                        <FileText class="h-3 w-3" /> Internal
                                        Notes
                                    </Label>
                                    <textarea
                                        id="notes"
                                        name="notes"
                                        rows="4"
                                        placeholder="Preferences, contact history, or specific billing details..."
                                        class="w-full resize-none rounded-xl border border-border/40 bg-muted/20 p-4 text-sm transition-all outline-none focus:bg-background focus:ring-2 focus:ring-primary/20"
                                    />
                                    <InputError :message="errors.notes" />
                                </div>
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-between border-t border-border/60 pt-6"
                        >
                            <Button
                                variant="ghost"
                                as-child
                                class="h-11 rounded-xl px-6 hover:bg-destructive/5 hover:text-destructive"
                            >
                                <Link :href="index().url">
                                    <ChevronLeft class="mr-2 h-4 w-4" /> Back to
                                    List
                                </Link>
                            </Button>

                            <Button
                                :disabled="processing"
                                class="h-11 rounded-xl bg-primary px-8 font-black text-primary-foreground shadow-lg shadow-primary/20 transition-all hover:-translate-y-0.5 hover:shadow-primary/30"
                            >
                                <PlusCircle class="mr-2 h-4 w-4" /> Create
                                Client
                            </Button>
                        </div>
                    </div>
                </Form>
            </div>
        </div>
    </AppLayout>
</template>
