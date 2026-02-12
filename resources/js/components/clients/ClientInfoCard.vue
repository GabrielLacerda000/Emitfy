<script setup lang="ts">
import { User, Mail, Building2, Calendar, FileText, BadgeInfo } from 'lucide-vue-next'; // Ícones para contexto
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
    <Card class="rounded-3xl border-none shadow-sm overflow-hidden bg-background">
        <div class="bg-primary/5 px-6 py-4 border-b border-primary/5">
            <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-primary/70 flex items-center gap-2">
                <span class="p-1 rounded-md bg-primary/10"><BadgeInfo class="h-3 w-3"/></span>
                Client Profile Details
            </h3>
        </div>

        <div class="p-6">
            <dl class="grid gap-x-8 gap-y-6 md:grid-cols-2">
                <div class="space-y-1.5 group">
                    <dt class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-muted-foreground/70">
                        <User class="h-3 w-3 text-primary/60" /> Name
                    </dt>
                    <dd class="text-sm font-bold text-foreground leading-none pl-5">
                        {{ client.name }}
                    </dd>
                </div>

                <div class="space-y-1.5 group">
                    <dt class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-muted-foreground/70">
                        <Mail class="h-3 w-3 text-primary/60" /> Email Address
                    </dt>
                    <dd class="text-sm font-bold text-foreground break-words pl-5">
                        {{ client.email }}
                    </dd>
                </div>

                <div class="space-y-1.5 group transition-opacity" :class="{ 'opacity-40': !client.company_name }">
                    <dt class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-muted-foreground/70">
                        <Building2 class="h-3 w-3 text-primary/60" /> Company
                    </dt>
                    <dd class="text-sm font-bold text-foreground pl-5">
                        {{ client.company_name || 'Individual Client' }}
                    </dd>
                </div>

                <div class="space-y-1.5 group">
                    <dt class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-muted-foreground/70">
                        <Calendar class="h-3 w-3 text-primary/60" /> Last Interaction
                    </dt>
                    <dd class="text-sm font-bold text-foreground pl-5">
                        <span v-if="lastInvoiceSent" class="inline-flex items-center gap-1.5">
                            {{ formatDate(lastInvoiceSent.sent_at!) }}
                            <span class="text-[9px] px-1.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-600 uppercase tracking-tighter">Sent</span>
                        </span>
                        <span v-else class="text-muted-foreground/50 font-medium italic">No invoices yet</span>
                    </dd>
                </div>

                <div v-if="client.notes" class="md:col-span-2 pt-4 mt-2 border-t border-border/40">
                    <dt class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-muted-foreground/70 mb-2">
                        <FileText class="h-3 w-3 text-primary/60" /> Client Notes
                    </dt>
                    <dd class="text-sm leading-relaxed text-muted-foreground bg-muted/30 p-4 rounded-2xl border border-border/20 italic whitespace-pre-wrap">
                        "{{ client.notes }}"
                    </dd>
                </div>
            </dl>
        </div>
    </Card>
</template>