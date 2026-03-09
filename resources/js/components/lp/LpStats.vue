<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import LpSection from './LpSection.vue';

const { t } = useI18n();

const stats = [
    { key: 'stat1' },
    { key: 'stat2' },
    { key: 'stat3' },
    { key: 'stat4' },
] as const;
</script>

<template>
    <LpSection class="relative overflow-hidden py-20">
        <div
            class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(ellipse_at_center,hsl(var(--primary)/0.06)_0%,transparent_70%)]"
        />

        <div class="text-center mb-14">
            <span
                class="mb-4 inline-block rounded-full bg-primary/10 px-3 py-1 text-xs font-semibold tracking-wider text-primary uppercase"
            >
                {{ t('lp.stats.badge') }}
            </span>
            <h2
                class="text-3xl font-extrabold tracking-tight sm:text-4xl lg:text-5xl mb-4"
            >
                {{ t('lp.stats.h2') }}
            </h2>
            <p class="text-lg text-muted-foreground max-w-2xl mx-auto">
                {{ t('lp.stats.subtitle') }}
            </p>
        </div>

        <div
            class="grid grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-4"
        >
            <div
                v-for="stat in stats"
                :key="stat.key"
                class="flex flex-col items-center text-center gap-3 p-6 rounded-2xl border border-border/50 bg-card/50"
            >
                <span
                    class="text-5xl font-black tracking-tight text-primary"
                >
                    {{ t(`lp.stats.${stat.key}.value`) }}
                </span>
                <span class="text-sm text-muted-foreground leading-snug max-w-[140px]">
                    {{ t(`lp.stats.${stat.key}.label`) }}
                </span>
            </div>
        </div>
    </LpSection>
</template>
