<script setup lang="ts">
import { register } from '@/routes';
import LpPricingCard from './LpPricingCard.vue';
import LpSection from './LpSection.vue';
</script>

<template>
    <LpSection background="muted" id="pricing" class="py-24">
        <div class="mb-20 text-center">
            <h2 class="mb-4 text-4xl font-extrabold tracking-tight sm:text-5xl">
                Simple Pricing for
                <span class="text-primary">Growing Businesses</span>
            </h2>
            <p class="mx-auto max-w-2xl text-lg text-muted-foreground">
                Start small and upgrade as your business grows. No hidden fees.
                No surprises.
            </p>
        </div>

        <div
            class="mx-auto grid max-w-7xl grid-cols-1 gap-8 px-4 lg:grid-cols-3"
        >
            <!-- STARTER -->
            <LpPricingCard
                name="Starter"
                price="R$29"
                description="Perfect for freelancers and small businesses starting out."
                :features="[
                    { text: 'Up to 20 clients', included: true },
                    { text: 'Up to 50 emails per month', included: true },
                    { text: 'Unlimited invoices', included: true },
                    { text: 'Basic dashboard', included: true },
                    { text: 'Email support', included: true },
                    { text: 'Recurring invoices', included: false },
                ]"
                cta="Get Started"
                :cta-href="register().url"
            />

            <!-- PRO (MOST POPULAR) -->
            <LpPricingCard
                name="Pro"
                price="R$69"
                highlighted
                description="For professionals who want automation and scale."
                :features="[
                    { text: 'Up to 300 clients', included: true },
                    { text: 'Up to 1000 emails per month', included: true },
                    { text: 'Unlimited invoices', included: true },
                    { text: 'Recurring invoices', included: true },
                    { text: 'Advanced reports', included: true },
                    { text: 'WhatsApp support', included: true },
                    { text: 'Priority support', included: true },
                ]"
                cta="Upgrade to Pro"
                :cta-href="register().url"
                savings="Most Popular"
            />

            <!-- BUSINESS -->
            <LpPricingCard
                name="Business"
                price="R$119"
                description="For high-volume billing and serious growth."
                :features="[
                    { text: 'Unlimited clients', included: true },
                    { text: 'Up to 5000 emails per month', included: true },
                    { text: 'Everything in Pro', included: true },
                    { text: 'Dedicated support', included: true },
                ]"
                cta="Scale My Business"
                :cta-href="register().url"
            />
        </div>

        <div
            class="mx-auto mt-16 max-w-3xl rounded-2xl border border-primary/10 bg-primary/5 p-8"
        >
            <div
                class="flex flex-col items-center justify-between gap-6 md:flex-row"
            >
                <div class="text-left">
                    <h4 class="text-lg font-bold">Why choose Pro?</h4>
                    <p class="text-sm text-muted-foreground">
                        Automating recurring invoices and reminders can save you
                        hours every month — time you can reinvest into growing
                        your business.
                    </p>
                </div>
                <div class="text-2xl font-black text-primary">Best Value</div>
            </div>
        </div>
    </LpSection>
</template>
