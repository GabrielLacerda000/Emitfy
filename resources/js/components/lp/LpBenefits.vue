<script setup lang="ts">
import { Clock, TrendingUp, Search, Award, Zap } from 'lucide-vue-next';
import LpFeatureCard from './LpFeatureCard.vue';
import LpSection from './LpSection.vue';
</script>

<template>
    <LpSection background="muted">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold tracking-tight sm:text-4xl lg:text-5xl mb-4">
                Why Freelancers & Small Businesses Choose Emitte
            </h2>
            <p class="text-lg text-muted-foreground max-w-2xl mx-auto">
                Real outcomes that impact your bottom line and free up your
                time.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <LpFeatureCard
                :icon="Clock"
                title="Save 10+ Hours Monthly"
                description="Stop copying data between spreadsheets. Automate invoice creation and tracking so you can focus on billable work."
            />
            <LpFeatureCard
                :icon="TrendingUp"
                title="Get Paid 40% Faster"
                description="Professional invoices and automated reminders mean clients pay sooner. No more awkward follow-up emails."
            />
            <LpFeatureCard
                :icon="Search"
                title="Never Lose Track"
                description="Know exactly what's outstanding. See which invoices are overdue at a glance. Never let revenue slip through the cracks."
            />
            <LpFeatureCard
                :icon="Award"
                title="Look Professional"
                description="Polished, branded invoices make the right impression. Win higher-paying clients who value quality."
            />
            <LpFeatureCard
                :icon="Zap"
                title="Scale Without Chaos"
                description="Going from 5 to 50 clients? Our system grows with you. No spreadsheet meltdowns when business booms."
            />
            <LpFeatureCard
                :icon="TrendingUp"
                title="See Revenue Clearly"
                description="Beautiful dashboards show your revenue pipeline. Plan ahead with confidence instead of guessing your cash flow."
            />
        </div>
    </LpSection>
</template>
