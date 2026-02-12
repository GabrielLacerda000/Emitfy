<script setup lang="ts">
import { Button } from '@/components/ui/button';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import LpSection from './LpSection.vue';
import { register } from '@/routes';
</script>

<template>
    <LpSection spacing="spacious">
        <div
            class="grid grid-cols-1 gap-12 lg:grid-cols-5 lg:gap-16 items-center"
        >
            <!-- Hero Text -->
            <div class="lg:col-span-2 space-y-6">
                <h1
                    class="text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl"
                >
                    Invoice Like a Pro,<br />
                    <span class="text-primary">Get Paid Faster</span>
                </h1>
                <p class="text-lg text-muted-foreground leading-relaxed">
                    Stop wrestling with spreadsheets and chasing late payments.
                    Emitte helps freelancers and small businesses create
                    professional invoices, track payments, and get paid 40%
                    faster.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <Button size="lg" as-child>
                        <a :href="register()">
                            Start Invoicing Free
                            <svg
                                class="ml-2 h-4 w-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6"
                                />
                            </svg>
                        </a>
                    </Button>
                    <Button variant="outline" size="lg" as-child>
                        <a href="#how-it-works">See How It Works</a>
                    </Button>
                </div>
                <p class="text-sm text-muted-foreground">
                    No credit card required. Free forever.
                </p>
            </div>

            <!-- Hero Image -->
            <div class="lg:col-span-3">
                <div
                    class="relative w-full aspect-[16/10] rounded-xl overflow-hidden border border-border/40 bg-card shadow-2xl"
                >
                    <PlaceholderPattern />
                    <div
                        class="absolute inset-0 flex items-center justify-center"
                    >
                        <p class="text-muted-foreground font-medium">
                            Dashboard Preview
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </LpSection>
</template>
