<script setup lang="ts">
import { Users, FileText, Send, DollarSign } from 'lucide-vue-next';
import LpSection from './LpSection.vue';
import LpStepCard from './LpStepCard.vue';
</script>

<template>
    <LpSection id="how-it-works">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold tracking-tight sm:text-4xl lg:text-5xl mb-4">
                From Invoice to Payment in 4 Simple Steps
            </h2>
            <p class="text-lg text-muted-foreground max-w-2xl mx-auto">
                We've streamlined the entire invoicing process. Here's how it
                works.
            </p>
        </div>

        <div
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12"
        >
            <LpStepCard
                :step="1"
                :icon="Users"
                title="Add Your Clients"
                description="Quickly import or add client details. Store contact info, payment terms, and preferences in one place."
            />
            <LpStepCard
                :step="2"
                :icon="FileText"
                title="Create Invoice"
                description="Generate professional invoices in seconds. Add line items, taxes, and custom notes with our intuitive editor."
            />
            <LpStepCard
                :step="3"
                :icon="Send"
                title="Send & Track"
                description="Email invoices directly or download PDFs. See when clients open them and get notified on status changes."
            />
            <LpStepCard
                :step="4"
                :icon="DollarSign"
                title="Get Paid"
                description="Track payment status in real-time. Mark invoices as paid and watch your revenue dashboard update automatically."
            />
        </div>
    </LpSection>
</template>
