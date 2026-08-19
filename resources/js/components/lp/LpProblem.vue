<script setup lang="ts">
import {
    FileQuestion,
    AlertCircle,
    FileX,
    TrendingDown,
} from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';
import LpSection from './LpSection.vue';

const { t } = useI18n();

const problems = [
    {
        // icon: FileQuestion,
        iconClass: 'text-red-500/70',
        ordinal: '01',
        titleKey: 'lp.problem.card1.title',
        descKey: 'lp.problem.card1.desc',
    },
    {
        // icon: AlertCircle,
        iconClass: 'text-orange-500/70',
        ordinal: '02',
        titleKey: 'lp.problem.card2.title',
        descKey: 'lp.problem.card2.desc',
    },
    {
        // icon: FileX,
        iconClass: 'text-amber-500/70',
        ordinal: '03',
        titleKey: 'lp.problem.card3.title',
        descKey: 'lp.problem.card3.desc',
    },
    {
        // icon: TrendingDown,
        iconClass: 'text-indigo-500/70',
        ordinal: '04',
        titleKey: 'lp.problem.card4.title',
        descKey: 'lp.problem.card4.desc',
    },
];
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

            <div class="overflow-hidden rounded-xl divide-y divide-border/40">
                <div
                    v-for="(problem, i) in problems"
                    :key="problem.ordinal"
                    class="group flex flex-col gap-4 px-6 py-8 transition-colors duration-200 md:flex-row md:items-start md:gap-10 md:px-8 md:py-10"
                    :class="i % 2 !== 0 ? 'bg-card/40 hover:bg-card/60' : 'hover:bg-card/30'"
                >
                    <div
                        class="flex flex-row items-center gap-3 md:w-16 md:flex-col md:items-start md:gap-2"
                    >
                        <span
                            class="text-4xl font-bold leading-none text-muted-foreground/20 tabular-nums md:text-5xl"
                        >
                            {{ problem.ordinal }}
                        </span>

                        <!-- <component
                            :is="problem.icon"
                            class="h-5 w-5 shrink-0 transition-transform duration-200 group-hover:scale-110"
                            :class="problem.iconClass"
                        /> -->
                    </div>

                    <div class="space-y-2">
                        <h3
                            class="text-xl font-semibold tracking-tight text-foreground transition-colors duration-200 group-hover:text-[oklch(0.62_0.12_80)]"
                        >
                            {{ t(problem.titleKey) }}
                        </h3>
                        <p class="max-w-xl leading-relaxed text-muted-foreground">
                            {{ t(problem.descKey) }}
                        </p>
                    </div>
                </div>
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
