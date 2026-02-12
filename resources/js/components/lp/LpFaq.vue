<script setup lang="ts">
import LpFaqItem from './LpFaqItem.vue';
import LpSection from './LpSection.vue';
</script>

<template>
    <LpSection id="faq">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold tracking-tight sm:text-4xl lg:text-5xl mb-4">
                Questions? We've Got Answers
            </h2>
            <p class="text-lg text-muted-foreground max-w-2xl mx-auto">
                Everything you need to know about Emitte.
            </p>
        </div>

        <div class="max-w-3xl mx-auto">
            <LpFaqItem
                question="Is Emitte really free?"
                answer="Yes! Our Basic plan is completely free forever. You get 10 invoices per month, unlimited clients, and all core features. Upgrade to Pro when you need unlimited invoicing and advanced features."
            />
            <LpFaqItem
                question="Can I customize my invoices?"
                answer="Absolutely. Pro users can add custom branding including logos, colors, and custom footer text. All users can customize invoice details, line items, taxes, and notes."
            />
            <LpFaqItem
                question="How do I get paid?"
                answer="Emitte helps you track payments but doesn't process them directly. You can send invoices via email or download PDFs, and mark invoices as paid when you receive payment through your preferred method (bank transfer, PayPal, Stripe, etc.)."
            />
            <LpFaqItem
                question="Can I import my existing clients?"
                answer="Yes! You can manually add clients one by one, or upload a CSV file to import multiple clients at once. We support standard formats from Excel and Google Sheets."
            />
            <LpFaqItem
                question="What if I need help?"
                answer="We're here for you! Basic users get email support with responses within 24-48 hours. Pro users get priority support with faster response times. We also have comprehensive documentation and video tutorials."
            />
        </div>
    </LpSection>
</template>
