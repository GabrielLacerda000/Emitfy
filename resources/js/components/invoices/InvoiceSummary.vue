<script setup lang="ts">
import { Calculator, Receipt } from 'lucide-vue-next';
import { Card } from '@/components/ui/card';
import { formatBRL } from '@/lib/utils';

interface Props {
    subtotal: string;
    tax: number | string;
    total: string;
}

defineProps<Props>();
</script>

<template>
    <Card
        class="relative overflow-hidden rounded-3xl border-none bg-background p-6 shadow-sm"
    >
        <Receipt
            class="absolute -top-4 -right-4 h-24 w-24 -rotate-12 opacity-[0.03]"
        />

        <div class="relative space-y-4">
            <div class="mb-2 flex items-center gap-2">
                <Calculator class="h-4 w-4" />
                <h3
                    class="text-[10px] font-black tracking-[0.2em] uppercase"
                >
                    Financial Summary
                </h3>
            </div>

            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-bold italic"
                        >Subtotal</span
                    >
                    <span class="text-sm font-bold text-foreground">
                        {{ formatBRL(subtotal) }}
                    </span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-sm font-bold italic"
                        >Taxes & Fees</span
                    >
                    <span class="text-sm font-bold text-rose-500/80">
                        + {{ formatBRL(tax) }}
                    </span>
                </div>

                <div class="relative py-2">
                    <div
                        class="absolute inset-0 flex items-center"
                        aria-hidden="true"
                    >
                        <div
                            class="w-full border-t border-dashed border-border/60"
                        ></div>
                    </div>
                </div>

                <div class="flex items-end justify-between pt-1">
                    <div class="flex flex-col">
                        <span
                            class="text-[10px] leading-none font-black tracking-widest text-primary/60 uppercase"
                        >
                            Grand Total
                        </span>
                        <span
                            class="mt-1 text-xs font-medium italic"
                            >Final amount to receive</span
                        >
                    </div>

                    <div class="text-right">
                        <span
                            class="text-3xl font-black tracking-tighter text-foreground"
                        >
                            {{ formatBRL(total) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </Card>
</template>
