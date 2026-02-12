<script setup lang="ts">
import type { Component } from 'vue';
import { Badge } from '@/components/ui/badge';

defineProps<{
    step: number;
    icon: Component;
    title: string;
    description: string;
}>();
</script>

<template>
    <div class="relative flex flex-col items-start">
        <Badge
            variant="outline"
            class="mb-4 h-8 w-8 items-center justify-center rounded-full border-primary/20 bg-primary/10 p-0 text-sm font-bold text-primary"
        >
            {{ step }}
        </Badge>
        <div
            class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-card border border-border/40"
        >
            <component :is="icon" class="h-6 w-6 text-foreground" />
        </div>
        <h3 class="mb-2 text-xl font-semibold">{{ title }}</h3>
        <p class="text-sm leading-relaxed text-muted-foreground">
            {{ description }}
        </p>
    </div>
</template>
