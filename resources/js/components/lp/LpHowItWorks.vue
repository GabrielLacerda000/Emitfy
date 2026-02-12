<script setup lang="ts">
import { Users, FileText, Send, DollarSign } from 'lucide-vue-next';
import LpSection from './LpSection.vue';
import LpStepCard from './LpStepCard.vue';
</script>

<template>
    <LpSection id="how-it-works" class="relative overflow-hidden py-24 lg:py-32">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[400px] bg-primary/5 blur-[120px] rounded-full -z-10" />

        <div class="text-center mb-20">
            <h2 class="text-4xl font-extrabold tracking-tight sm:text-5xl lg:text-6xl mb-6 bg-linear-to-b from-foreground to-foreground/70 bg-clip-text text-transparent">
                From Invoice to Payment in 4 Simple Steps
            </h2>
            <p class="text-xl text-muted-foreground max-w-3xl mx-auto leading-relaxed">
                Stop chasing paperwork. We've built a seamless workflow that lets you focus on your work while we handle the billing.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-12 lg:gap-16 px-4">
            <LpStepCard
                :step="1"
                :icon="Users"
                title="Add Your Clients"
                description="Quickly import or add client details. Store contact info and payment terms in one place."
            />
            <LpStepCard
                :step="2"
                :icon="FileText"
                title="Create Invoice"
                description="Generate professional invoices in seconds. Add line items and taxes with our intuitive editor."
            />
            <LpStepCard
                :step="3"
                :icon="Send"
                title="Send & Track"
                description="Email invoices directly. See when clients open them and get real-time status notifications."
            />
            <LpStepCard
                :step="4"
                :icon="DollarSign"
                title="Get Paid"
                description="Mark invoices as paid and watch your revenue dashboard update automatically."
                :is-last="true"
            />
        </div>
    </LpSection>
</template>
