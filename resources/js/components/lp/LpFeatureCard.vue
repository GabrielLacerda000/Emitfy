<script setup lang="ts">
import type { Component } from 'vue';
import { Card, CardContent } from '@/components/ui/card';

defineProps<{
    icon: Component;
    iconClass?: string;
    title: string;
    description: string;
}>();
</script>

<template>
    <Card class="group relative overflow-hidden border-border/50 bg-card/50 backdrop-blur-sm transition-all duration-300 hover:border-primary/30 hover:shadow-2xl hover:shadow-primary/5 hover:-translate-y-1">
        <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-primary/5 blur-2xl transition-opacity opacity-0 group-hover:opacity-100" />

        <CardContent class="p-8">
            <div class="flex flex-col sm:flex-row gap-6 items-start">
                <div
                    :class="[
                        'flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl shadow-sm transition-transform duration-500 group-hover:scale-110 group-hover:rotate-3',
                        iconClass || 'bg-primary/10 text-primary',
                    ]"
                >
                    <component :is="icon" class="h-7 w-7" />
                </div>

                <div class="space-y-3">
                    <h3 class="text-xl font-bold tracking-tight text-foreground/90 group-hover:text-primary transition-colors">
                        {{ title }}
                    </h3>
                    <p class="text-base leading-relaxed text-muted-foreground/80">
                        {{ description }}
                    </p>
                </div>
            </div>
        </CardContent>
        
        <div class="absolute bottom-0 left-0 h-[2px] w-0 bg-primary/40 transition-all duration-500 group-hover:w-full" />
    </Card>
</template>