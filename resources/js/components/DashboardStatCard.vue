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
    <Card class="transition-all hover:ring-1 hover:ring-primary/50 shadow-sm">
        <CardContent class="p-4">
            <div class="flex flex-col gap-2">
                <div class="flex items-center justify-between">
                    <p class="text-md font-semibold tracking-tight text-muted-foreground uppercase">
                        {{ title }}
                    </p>
                    <div
                        :class="[
                            'flex h-8 w-8 items-center justify-center rounded-md',
                            iconClass,
                        ]"
                    >
                        <component :is="icon" class="h-4 w-4" />
                    </div>
                </div>

                <div>
                    <p class="text-xl font-bold tracking-tight">
                        {{ amount }}
                    </p>
                    <p class="text-sm text-muted-foreground leading-none mt-1">
                        {{ description }}
                    </p>
                </div>
            </div>
        </CardContent>
    </Card>
</template>
