<script setup lang="ts">
import { Button } from '@/components/ui/button';
import LpSection from './LpSection.vue';
import { register } from '@/routes';
import { ArrowRight } from 'lucide-vue-next';
</script>

<template>
    <LpSection background="primary" spacing="compact">
        <div class="text-center max-w-3xl mx-auto">
            <h2 class="text-3xl font-bold tracking-tight sm:text-4xl lg:text-5xl mb-4">
                Ready to Get Paid Faster?
            </h2>
            <p class="text-lg mb-8 opacity-90">
                Join thousands of freelancers and small businesses who've
                simplified their invoicing and accelerated their cash flow.
            </p>
            <Button
                size="lg"
                variant="secondary"
                class="text-lg px-8 py-6 h-auto"
                as-child
            >
                <a :href="register()">
                    Start Invoicing for Free
                    <ArrowRight class="ml-2 h-5 w-5" />
                </a>
            </Button>
            <p class="text-sm mt-4 opacity-80">
                No credit card required. Free forever.
            </p>
        </div>
    </LpSection>
</template>
