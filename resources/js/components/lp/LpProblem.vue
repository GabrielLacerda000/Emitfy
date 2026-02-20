<script setup lang="ts">
import {
    FileQuestion,
    AlertCircle,
    FileX,
    TrendingDown,
} from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';
import LpFeatureCard from './LpFeatureCard.vue';
import LpSection from './LpSection.vue';

const { t } = useI18n();
</script>

<template>
    <LpSection background="muted" class="relative overflow-hidden py-24">
        <div
            class="pointer-events-none absolute top-0 right-0 h-96 w-96 translate-x-1/4 -translate-y-1/2 rounded-full bg-red-500/5 blur-[100px]"
        />
        <div
            class="pointer-events-none absolute bottom-0 left-0 h-96 w-96 -translate-x-1/4 translate-y-1/2 rounded-full bg-primary/5 blur-[100px]"
        />

        <div class="relative z-10 container">
            <div
                class="mb-16 flex flex-col items-end justify-between gap-6 md:flex-row"
            >
                <div class="max-w-2xl text-left">
                    <span
                        class="mb-4 inline-block rounded-full bg-red-500/10 px-3 py-1 text-xs font-semibold tracking-wider text-red-600 uppercase dark:text-red-400"
                    >
                        {{ t('lp.problem.badge') }}
                    </span>
                    <h2
                        class="text-4xl font-extrabold tracking-tight text-foreground sm:text-5xl lg:text-6xl"
                    >
                        {{ t('lp.problem.h2') }}
                        <span class="text-red-500/80">{{
                            t('lp.problem.h2red')
                        }}</span>
                    </h2>
                </div>
                <p class="max-w-sm pb-2 text-xl text-muted-foreground">
                    {{ t('lp.problem.subtitle') }}
                </p>
            </div>

            <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                <LpFeatureCard
                    :icon="FileQuestion"
                    icon-class="bg-red-500/10 text-red-600 dark:text-red-400"
                    :title="t('lp.problem.card1.title')"
                    :description="t('lp.problem.card1.desc')"
                />
                <LpFeatureCard
                    :icon="AlertCircle"
                    icon-class="bg-orange-500/10 text-orange-600 dark:text-orange-400"
                    :title="t('lp.problem.card2.title')"
                    :description="t('lp.problem.card2.desc')"
                />
                <LpFeatureCard
                    :icon="FileX"
                    icon-class="bg-amber-500/10 text-amber-600 dark:text-amber-400"
                    :title="t('lp.problem.card3.title')"
                    :description="t('lp.problem.card3.desc')"
                />
                <LpFeatureCard
                    :icon="TrendingDown"
                    icon-class="bg-indigo-500/10 text-indigo-600 dark:text-indigo-400"
                    :title="t('lp.problem.card4.title')"
                    :description="t('lp.problem.card4.desc')"
                />
            </div>

            <div class="mt-16 text-center">
                <p
                    class="flex items-center justify-center gap-2 text-sm font-medium text-muted-foreground"
                >
                    <span class="h-px w-8 bg-border"></span>
                    {{ t('lp.problem.betterWay') }}
                    <span class="h-px w-8 bg-border"></span>
                </p>
            </div>
        </div>
    </LpSection>
</template>
