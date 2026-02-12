<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import LpBenefits from '@/components/lp/LpBenefits.vue';
import LpCta from '@/components/lp/LpCta.vue';
import LpFaq from '@/components/lp/LpFaq.vue';
import LpFooter from '@/components/lp/LpFooter.vue';
import LpHero from '@/components/lp/LpHero.vue';
import LpHowItWorks from '@/components/lp/LpHowItWorks.vue';
import LpPricing from '@/components/lp/LpPricing.vue';
import LpProblem from '@/components/lp/LpProblem.vue';
import LpScreenshot from '@/components/lp/LpScreenshot.vue';
import { Button } from '@/components/ui/button';
import { dashboard, login, register } from '@/routes';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);
</script>

<template>
    <Head title="Professional Invoice Management for Freelancers & Small Business">
        <meta
            name="description"
            content="Create professional invoices, track payments, and get paid 40% faster. Emitte helps freelancers and small businesses save 10+ hours monthly on invoice management."
        />
        <meta
            property="og:title"
            content="Emitte - Invoice Like a Pro, Get Paid Faster"
        />
        <meta
            property="og:description"
            content="Stop wrestling with spreadsheets. Create professional invoices in seconds and track payments effortlessly."
        />
        <meta property="og:type" content="website" />
    </Head>

    <div class="min-h-screen bg-background">
        <!-- Sticky Header -->
        <header
            class="sticky top-0 z-50 border-b border-border/40 bg-background/80 backdrop-blur-lg"
        >
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <!-- Logo -->
                    <Link href="/" class="flex items-center gap-2">
                        <svg
                            class="h-8 w-8 text-primary"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                            />
                        </svg>
                        <span class="text-xl font-bold">Emitte</span>
                    </Link>

                    <!-- Navigation -->
                    <nav class="hidden md:flex items-center gap-6">
                        <a
                            href="#how-it-works"
                            class="text-sm font-medium text-muted-foreground hover:text-foreground transition-colors"
                        >
                            How It Works
                        </a>
                        <a
                            href="#pricing"
                            class="text-sm font-medium text-muted-foreground hover:text-foreground transition-colors"
                        >
                            Pricing
                        </a>
                        <a
                            href="#faq"
                            class="text-sm font-medium text-muted-foreground hover:text-foreground transition-colors"
                        >
                            FAQ
                        </a>
                    </nav>

                    <!-- Auth Buttons -->
                    <div class="flex items-center gap-3">
                        <template v-if="$page.props.auth.user">
                            <Button variant="default" as-child>
                                <Link :href="dashboard()">Dashboard</Link>
                            </Button>
                        </template>
                        <template v-else>
                            <Button variant="ghost" as-child>
                                <Link :href="login()">Log in</Link>
                            </Button>
                            <Button v-if="canRegister" as-child>
                                <Link :href="register()">Get Started</Link>
                            </Button>
                        </template>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main>
            <LpHero />
            <LpProblem />
            <LpHowItWorks />
            <LpBenefits />
            <LpScreenshot />
            <LpPricing />
            <LpFaq />
            <LpCta />
        </main>

        <!-- Footer -->
        <LpFooter />
    </div>
</template>
