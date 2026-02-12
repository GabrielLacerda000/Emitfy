<script setup lang="ts">
import { Card } from '@/components/ui/card';
import { formatBRL } from '@/lib/utils';

interface Props {
    subtotal: string;
    tax: number;
    total: string;
}

defineProps<Props>();
</script>

<template>
    <Card class="p-6">
        <div class="space-y-3">
            <div class="flex items-center justify-between text-sm">
                <span class="text-base">Subtotal</span>
                <span class="font-medium">{{ formatBRL(subtotal) }}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="text-base">Tax</span>
                <span class="font-medium">{{ formatBRL(tax) }}</span>
            </div>
            <div
                class="flex items-center justify-between border-t border-sidebar-border/70 pt-3 dark:border-sidebar-border"
            >
                <span class="text-base font-semibold">Total</span>
                <span class="text-lg font-bold">{{ formatBRL(total) }}</span>
            </div>
        </div>
    </Card>
</template>
