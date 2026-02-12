<script setup lang="ts">
import type { Component } from 'vue';

defineProps<{
    step: number;
    icon: Component;
    title: string;
    description: string;
    isLast?: boolean;
}>();
</script>

<template>
    <div class="relative group flex flex-col items-center text-center lg:items-start lg:text-left">
        <div v-if="!isLast" 
             class="hidden lg:block absolute top-12 left-14 w-full h-[2px] bg-linear-to-rfrom-primary/20 to-transparent -z-10" />

        <div class="relative mb-6">
            <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-primary/3 border border-primary/10 transition-all duration-300 group-hover:border-primary/30 group-hover:bg-primary/6 group-hover:shadow-xl group-hover:shadow-primary/5">
                <component :is="icon" class="h-10 w-10 text-primary" />
            </div>
            
            <div class="absolute -top-2 -right-2 flex h-8 w-8 items-center justify-center rounded-full bg-primary text-primary-foreground text-sm font-bold shadow-lg">
                {{ step }}
            </div>
        </div>

        <h3 class="mb-3 text-2xl font-bold tracking-tight text-foreground">{{ title }}</h3>
        <p class="text-base leading-relaxed text-muted-foreground max-w-[280px]">
            {{ description }}
        </p>
    </div>
</template>