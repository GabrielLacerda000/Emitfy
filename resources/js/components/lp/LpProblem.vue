<script setup lang="ts">
import {
    FileQuestion,
    AlertCircle,
    FileX,
    TrendingDown,
} from 'lucide-vue-next';
import LpFeatureCard from './LpFeatureCard.vue';
import LpSection from './LpSection.vue';
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
                        The Pain Points
                    </span>
                    <h2
                        class="text-4xl font-extrabold tracking-tight text-foreground sm:text-5xl lg:text-6xl"
                    >
                        Tired of Invoice
                        <span class="text-red-500/80">Headaches?</span>
                    </h2>
                </div>
                <p class="max-w-sm pb-2 text-xl text-muted-foreground">
                    Freelancing is hard enough. Your billing process shouldn't
                    be the reason you're stressed.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                <LpFeatureCard
                    :icon="FileQuestion"
                    icon-class="bg-red-500/10 text-red-600 dark:text-red-400"
                    title="Lost in Spreadsheets"
                    description="Drowning in Excel files? Lose track of who owes what? Spend hours manually updating rows instead of doing the work you love."
                />
                <LpFeatureCard
                    :icon="AlertCircle"
                    icon-class="bg-orange-500/10 text-orange-600 dark:text-orange-400"
                    title="Chasing Payments"
                    description="Following up on overdue invoices feels awkward and wastes your valuable time. You shouldn't have to beg for money."
                />
                <LpFeatureCard
                    :icon="FileX"
                    icon-class="bg-amber-500/10 text-amber-600 dark:text-amber-400"
                    title="Looking Unprofessional"
                    description="Generic templates and inconsistent branding make you look like an amateur. First impressions matter for high-paying clients."
                />
                <LpFeatureCard
                    :icon="TrendingDown"
                    icon-class="bg-indigo-500/10 text-indigo-600 dark:text-indigo-400"
                    title="Missing Cash Flow Insights"
                    description="No idea what's coming in next month? Poor visibility into your revenue pipeline makes it impossible to plan and grow."
                />
            </div>

            <div class="mt-16 text-center">
                <p
                    class="flex items-center justify-center gap-2 text-sm font-medium text-muted-foreground"
                >
                    <span class="h-px w-8 bg-border"></span>
                    There is a better way
                    <span class="h-px w-8 bg-border"></span>
                </p>
            </div>
        </div>
    </LpSection>
</template>
