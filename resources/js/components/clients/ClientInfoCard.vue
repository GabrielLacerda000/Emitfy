<script setup lang="ts">
import {
    User,
    Mail,
    Building2,
    Calendar,
    FileText,
    BadgeInfo,
} from 'lucide-vue-next'; // Ícones para contexto
import { Card } from '@/components/ui/card';
import { formatDate } from '@/lib/utils';
import type { Client, Invoice } from '@/types';

interface Props {
    client: Client;
    lastInvoiceSent: Invoice | null;
}

defineProps<Props>();
</script>

<template>
    <Card
        class="overflow-hidden rounded-3xl border-none bg-background shadow-sm"
    >
        <div class="border-b border-primary/5 bg-primary/5 px-6 py-4">
            <h3
                class="flex items-center gap-2 text-[10px] font-black tracking-[0.2em] text-primary/70 uppercase"
            >
                <span class="rounded-md bg-primary/10 p-1"
                    ><BadgeInfo class="h-3 w-3"
                /></span>
                Client Profile Details
            </h3>
        </div>

        <div class="p-6">
            <dl class="grid gap-x-8 gap-y-6 md:grid-cols-2">
                <div class="group space-y-1.5">
                    <dt
                        class="flex items-center gap-2 text-[10px] font-black tracking-widest text-muted-foreground/70 uppercase"
                    >
                        <User class="h-3 w-3 text-primary/60" /> Name
                    </dt>
                    <dd
                        class="pl-5 text-sm leading-none font-bold text-foreground"
                    >
                        {{ client.name }}
                    </dd>
                </div>

                <div class="group space-y-1.5">
                    <dt
                        class="flex items-center gap-2 text-[10px] font-black tracking-widest text-muted-foreground/70 uppercase"
                    >
                        <Mail class="h-3 w-3 text-primary/60" /> Email Address
                    </dt>
                    <dd
                        class="pl-5 text-sm font-bold wrap-break-word text-foreground"
                    >
                        {{ client.email }}
                    </dd>
                </div>

                <div
                    class="group space-y-1.5 transition-opacity"
                    :class="{ 'opacity-40': !client.company_name }"
                >
                    <dt
                        class="flex items-center gap-2 text-[10px] font-black tracking-widest text-muted-foreground/70 uppercase"
                    >
                        <Building2 class="h-3 w-3 text-primary/60" /> Company
                    </dt>
                    <dd class="pl-5 text-sm font-bold text-foreground">
                        {{ client.company_name || 'Individual Client' }}
                    </dd>
                </div>

                <div class="group space-y-1.5">
                    <dt
                        class="flex items-center gap-2 text-[10px] font-black tracking-widest text-muted-foreground/70 uppercase"
                    >
                        <Calendar class="h-3 w-3 text-primary/60" /> Last
                        Interaction
                    </dt>
                    <dd class="pl-5 text-sm font-bold text-foreground">
                        <span
                            v-if="lastInvoiceSent"
                            class="inline-flex items-center gap-1.5"
                        >
                            {{ formatDate(lastInvoiceSent.sent_at!) }}
                            <span
                                class="rounded-full bg-emerald-500/10 px-1.5 py-0.5 text-[9px] tracking-tighter text-emerald-600 uppercase"
                                >Sent</span
                            >
                        </span>
                        <span
                            v-else
                            class="font-medium text-muted-foreground/50 italic"
                            >No invoices yet</span
                        >
                    </dd>
                </div>

                <div
                    v-if="client.notes"
                    class="mt-2 border-t border-border/40 pt-4 md:col-span-2"
                >
                    <dt
                        class="mb-2 flex items-center gap-2 text-[10px] font-black tracking-widest text-muted-foreground/70 uppercase"
                    >
                        <FileText class="h-3 w-3 text-primary/60" /> Client
                        Notes
                    </dt>
                    <dd
                        class="rounded-2xl border border-border/20 bg-muted/30 p-4 text-sm leading-relaxed whitespace-pre-wrap text-muted-foreground italic"
                    >
                        "{{ client.notes }}"
                    </dd>
                </div>
            </dl>
        </div>
    </Card>
</template>
