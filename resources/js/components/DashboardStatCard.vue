<script setup lang="ts">
import type { Component } from 'vue';
import { Card, CardContent } from '@/components/ui/card';

interface Props {
    title: string;
    amount: string;
    description: string;
    icon: Component;
    iconClass?: string;
    trend?: {
        value: string;
        positive: boolean;
    };
}

defineProps<Props>();
</script>

<template>
    <Card class="group relative overflow-hidden border-border/50 bg-card transition-all duration-300 hover:border-primary/30 hover:shadow-lg hover:shadow-primary/5 hover:-translate-y-1">
        <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-primary/5 blur-2xl transition-opacity opacity-0 group-hover:opacity-100" />

        <CardContent class="p-6">
            <div class="flex flex-col gap-4">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold tracking-wider text-muted-foreground uppercase">
                        {{ title }}
                    </p>
                    <div
                        :class="[
                            'flex h-10 w-10 items-center justify-center rounded-xl transition-all duration-500 group-hover:scale-110 group-hover:rotate-3 shadow-sm',
                            iconClass || 'bg-primary/10 text-primary',
                        ]"
                    >
                        <component :is="icon" class="h-5 w-5" />
                    </div>
                </div>

                <div class="space-y-1">
                    <h3 class="text-3xl font-black tracking-tight text-foreground transition-colors group-hover:text-primary/90">
                        {{ amount }}
                    </h3>
                    
                    <div class="flex items-center gap-2">
                        <span v-if="trend" :class="[
                            'text-[10px] font-bold px-1.5 py-0.5 rounded-md flex items-center',
                            trend.positive ? 'bg-emerald-500/10 text-emerald-600' : 'bg-red-500/10 text-red-600'
                        ]">
                            {{ trend.positive ? '↑' : '↓' }} {{ trend.value }}
                        </span>
                        
                        <p class="text-xs font-medium text-muted-foreground/80 leading-none">
                            {{ description }}
                        </p>
                    </div>
                </div>
            </div>
        </CardContent>
        
        <div class="absolute bottom-0 left-0 h-[2px] w-0 bg-primary/40 transition-all duration-500 group-hover:w-full" />
    </Card>
</template>