<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card';
import type { Component } from 'vue';

defineProps<{
    icon: Component;
    iconClass?: string;
    title: string;
    description: string;
}>();
</script>

<template>
    <Card class="h-full border-border/40 transition-shadow hover:shadow-md">
        <CardContent class="p-6">
            <div
                :class="[
                    'mb-4 flex h-12 w-12 items-center justify-center rounded-lg',
                    iconClass || 'bg-primary/10 text-primary',
                ]"
            >
                <component :is="icon" class="h-6 w-6" />
            </div>
            <h3 class="mb-2 text-lg font-semibold">{{ title }}</h3>
            <p class="text-sm leading-relaxed text-muted-foreground">
                {{ description }}
            </p>
        </CardContent>
    </Card>
</template>
