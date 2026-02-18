<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import LpSection from './LpSection.vue';

const { t } = useI18n();
</script>

<template>
    <LpSection id="demo">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold tracking-tight sm:text-4xl lg:text-5xl mb-4">
                {{ t('lp.screenshot.h2') }}
            </h2>
            <p class="text-lg text-muted-foreground max-w-2xl mx-auto">
                {{ t('lp.screenshot.subtitle') }}
            </p>
        </div>

        <div class="max-w-6xl mx-auto">
            <div
                class="relative w-full aspect-16/10 rounded-xl overflow-hidden border border-border/40 bg-card shadow-2xl"
            >
                <PlaceholderPattern />
                <div class="absolute inset-0 flex items-center justify-center">
                    <p class="text-muted-foreground font-medium text-lg">
                        {{ t('lp.screenshot.placeholder') }}
                    </p>
                </div>
            </div>
        </div>
    </LpSection>
</template>
