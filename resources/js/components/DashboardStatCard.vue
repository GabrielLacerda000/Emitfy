<script setup lang="ts">
import type { Component } from 'vue';
import { Card, CardContent } from '@/components/ui/card';

interface Props {
    title: string;
    amount: string;
    description: string;
    icon: Component;
    iconClass?: string;
}

defineProps<Props>();
</script>

<template>
    <Card class="transition-all hover:ring-2 hover:ring-primary">
        <CardContent class="p-3">
            <div class="flex items-start justify-between">
                <div class="flex flex-col flex-1 gap-3">
                    <div
                        :class="[
                            'flex h-12 w-12 items-center justify-center rounded-full bg-muted/50',
                            iconClass,
                        ]"
                    >
                        <component :is="icon" class="h-6 w-6" />
                    </div>

                    <p class="text-sm font-medium text-muted-foreground">
                        {{ title }}
                    </p>
                    <p class="mt-2 text-3xl font-bold">
                        {{ amount }}
                    </p>
                    <p class="mt-1 text-sm text-muted-foreground">
                        {{ description }}
                    </p>
                </div>
            </div>
        </CardContent>
    </Card>
</template>
