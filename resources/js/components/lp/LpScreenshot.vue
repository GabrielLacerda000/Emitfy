<script setup lang="ts">
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import LpSection from './LpSection.vue';
</script>

<template>
    <LpSection>
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold tracking-tight sm:text-4xl lg:text-5xl mb-4">
                Everything You Need in One Beautiful Dashboard
            </h2>
            <p class="text-lg text-muted-foreground max-w-2xl mx-auto">
                Invoice management shouldn't be complicated. Emitte brings
                clarity to your entire billing workflow.
            </p>
        </div>

        <div class="max-w-6xl mx-auto">
            <div
                class="relative w-full aspect-[16/10] rounded-xl overflow-hidden border border-border/40 bg-card shadow-2xl"
            >
                <PlaceholderPattern />
                <div class="absolute inset-0 flex items-center justify-center">
                    <p class="text-muted-foreground font-medium text-lg">
                        Full Dashboard View
                    </p>
                </div>
            </div>
        </div>
    </LpSection>
</template>
