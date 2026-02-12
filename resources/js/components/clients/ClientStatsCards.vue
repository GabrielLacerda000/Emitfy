<script setup lang="ts">
import { CheckCircle, Clock, FileText, AlertCircle } from 'lucide-vue-next';
import { computed } from 'vue';
import { Card } from '@/components/ui/card';
import { formatBRL } from '@/lib/utils';
import type { ClientStats } from '@/types';

const props = defineProps<{ stats: ClientStats }>();

const statConfig = computed(() => [
    {
        label: 'Total Paid',
        value: props.stats.totalPaid,
        count: props.stats.totalPaidCount,
        icon: CheckCircle,
        bgClass: 'bg-emerald-500/10',
        iconClass: 'text-emerald-600',
        dotClass: 'bg-emerald-500',
    },
    {
        label: 'Total Pending',
        value: props.stats.totalPending,
        count: props.stats.totalPendingCount,
        icon: Clock,
        bgClass: 'bg-blue-500/10',
        iconClass: 'text-blue-600',
        dotClass: 'bg-blue-500',
    },
    {
        label: 'Total Overdue',
        value: props.stats.totalOverdue,
        count: props.stats.totalOverdueCount,
        icon: AlertCircle,
        bgClass: 'bg-rose-500/10',
        iconClass: 'text-rose-600',
        dotClass: 'bg-rose-500',
    },
    {
        label: 'Total Draft',
        value: props.stats.totalDraft,
        count: props.stats.totalDraftCount,
        icon: FileText,
        bgClass: 'bg-slate-500/10',
        iconClass: 'text-slate-600',
        dotClass: 'bg-slate-500',
    },
]);
</script>

<template>
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <Card
            v-for="item in statConfig"
            :key="item.label"
            class="rounded-3xl border-none bg-background p-5 shadow-sm"
        >
            <div class="space-y-4">
                <div :class="['w-fit rounded-xl p-2.5', item.bgClass]">
                    <component
                        :is="item.icon"
                        :class="['h-5 w-5', item.iconClass]"
                    />
                </div>

                <div class="space-y-1">
                    <p
                        class="text-[10px] font-black tracking-widest text-muted-foreground/60 uppercase"
                    >
                        {{ item.label }}
                    </p>
                    <h2
                        class="text-lg font-black tracking-tight break-all xl:text-xl"
                    >
                        {{ formatBRL(item.value) }}
                    </h2>
                    <p class="text-[10px] font-bold text-muted-foreground/50">
                        {{ item.count }} itens
                    </p>
                </div>
            </div>
        </Card>
    </div>
</template>
