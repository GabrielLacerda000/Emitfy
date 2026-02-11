<script setup lang="ts">
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
    <Card class="p-6">
        <h3 class="mb-4 text-lg font-semibold">Client Details</h3>
        <dl class="grid gap-4 text-sm md:grid-cols-2">
            <div>
                <dt class="font-medium text-muted-foreground">Name</dt>
                <dd class="mt-1">{{ client.name }}</dd>
            </div>
            <div>
                <dt class="font-medium text-muted-foreground">Email</dt>
                <dd class="mt-1">{{ client.email }}</dd>
            </div>
            <div v-if="client.company_name">
                <dt class="font-medium text-muted-foreground">Company</dt>
                <dd class="mt-1">{{ client.company_name }}</dd>
            </div>
            <div v-if="lastInvoiceSent">
                <dt class="font-medium text-muted-foreground">
                    Last Invoice Sent
                </dt>
                <dd class="mt-1">
                    {{ formatDate(lastInvoiceSent.sent_at!) }}
                </dd>
            </div>
            <div v-if="client.notes" class="md:col-span-2">
                <dt class="font-medium text-muted-foreground">Notes</dt>
                <dd class="mt-1 whitespace-pre-wrap">
                    {{ client.notes }}
                </dd>
            </div>
        </dl>
    </Card>
</template>
