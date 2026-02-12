<script setup lang="ts">
import {
    FileQuestion,
    AlertCircle,
    FileX,
    TrendingDown,
} from 'lucide-vue-next';
import LpSection from './LpSection.vue';
import LpFeatureCard from './LpFeatureCard.vue';
</script>

<template>
    <LpSection background="muted">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold tracking-tight sm:text-4xl lg:text-5xl mb-4">
                Tired of Invoice Headaches?
            </h2>
            <p class="text-lg text-muted-foreground max-w-2xl mx-auto">
                You're not alone. Here's what freelancers tell us keeps them up
                at night.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                description="Following up on overdue invoices feels awkward and wastes your valuable time. You shouldn't have to beg for money you've already earned."
            />
            <LpFeatureCard
                :icon="FileX"
                icon-class="bg-yellow-500/10 text-yellow-600 dark:text-yellow-400"
                title="Looking Unprofessional"
                description="Generic templates and inconsistent branding make you look like an amateur. First impressions matter, especially to high-paying clients."
            />
            <LpFeatureCard
                :icon="TrendingDown"
                icon-class="bg-purple-500/10 text-purple-600 dark:text-purple-400"
                title="Missing Cash Flow Insights"
                description="No idea what's coming in next month? Poor visibility into your revenue pipeline makes it impossible to plan and grow your business."
            />
        </div>
    </LpSection>
</template>
