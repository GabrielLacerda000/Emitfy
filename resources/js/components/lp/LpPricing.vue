<script setup lang="ts">
import LpSection from './LpSection.vue';
import LpPricingCard from './LpPricingCard.vue';
import { register } from '@/routes';
</script>

<template>
    <LpSection background="muted" id="pricing">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold tracking-tight sm:text-4xl lg:text-5xl mb-4">
                Simple Pricing That Grows With You
            </h2>
            <p class="text-lg text-muted-foreground max-w-2xl mx-auto">
                Start free and upgrade when you're ready. No hidden fees, no
                surprises.
            </p>
        </div>

        <div
            class="grid grid-cols-1 lg:grid-cols-2 gap-8 max-w-5xl mx-auto items-start"
        >
            <LpPricingCard
                name="Basic"
                price="Free"
                description="Perfect for freelancers just getting started"
                :features="[
                    { text: '10 invoices per month', included: true },
                    { text: 'Unlimited clients', included: true },
                    { text: 'PDF export', included: true },
                    { text: 'Basic dashboard', included: true },
                    { text: 'Email support', included: true },
                    { text: 'Custom branding', included: false },
                    { text: 'Advanced reporting', included: false },
                    { text: 'Priority support', included: false },
                ]"
                cta="Get Started Free"
                :cta-href="register()"
            />

            <LpPricingCard
                name="Pro"
                price="$12"
                description="For growing businesses that need more power"
                :features="[
                    { text: 'Unlimited invoices', included: true },
                    { text: 'Unlimited clients', included: true },
                    { text: 'PDF export', included: true },
                    { text: 'Advanced dashboard', included: true },
                    { text: 'Priority support', included: true },
                    { text: 'Custom branding', included: true },
                    { text: 'Advanced reporting & analytics', included: true },
                    { text: 'Recurring invoices', included: true },
                ]"
                cta="Start Pro Trial"
                :cta-href="register()"
                :highlighted="true"
            />
        </div>

        <p class="text-center text-sm text-muted-foreground mt-8">
            All plans include unlimited clients and secure data storage. Cancel
            anytime.
        </p>
    </LpSection>
</template>
