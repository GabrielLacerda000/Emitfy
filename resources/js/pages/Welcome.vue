<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Globe } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
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
import { useLocale } from '@/composables/useLocale';
import { dashboard, login, register } from '@/routes';

const { t } = useI18n();
const { locale, setLocale } = useLocale();

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
    <Head
        title="Professional Invoice Management for Freelancers & Small Business"
    >
        <meta
            name="description"
            content="Create professional invoices, track payments, and get paid 40% faster. Emitfy helps freelancers and small businesses save 10+ hours monthly on invoice management."
        />
        <meta
            property="og:title"
            content="Emitfy - Invoice Like a Pro, Get Paid Faster"
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
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <!-- Logo -->
                    <Link href="/" class="flex items-center gap-2">
                        <AppLogoIcon class="size-9 text-primary" />
                        <span class="text-xl font-bold">Emitfy</span>
                    </Link>

                    <!-- Navigation -->
                    <nav class="hidden items-center gap-6 md:flex">
                        <a
                            href="#how-it-works"
                            class="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground"
                        >
                            {{ t('lp.nav.howItWorks') }}
                        </a>
                        <a
                            href="#pricing"
                            class="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground"
                        >
                            {{ t('lp.nav.pricing') }}
                        </a>
                        <a
                            href="#faq"
                            class="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground"
                        >
                            {{ t('lp.nav.faq') }}
                        </a>
                    </nav>

                    <!-- Auth Buttons + Locale Toggle -->
                    <div class="flex items-center gap-3">
                        <!-- Language Toggle -->
                        <button
                            @click="setLocale(locale === 'en' ? 'pt-BR' : 'en')"
                            class="flex items-center gap-1.5 rounded-lg border border-border/60 px-3 py-1.5 text-xs font-bold text-muted-foreground transition-all hover:border-primary/40 hover:text-primary"
                        >
                            <Globe class="h-3.5 w-3.5" />
                            {{ locale === 'en' ? 'EN' : 'PT' }}
                        </button>

                        <template v-if="$page.props.auth.user">
                            <Button variant="default" as-child>
                                <Link :href="dashboard()">{{
                                    t('lp.nav.dashboard')
                                }}</Link>
                            </Button>
                        </template>
                        <template v-else>
                            <Button variant="ghost" as-child>
                                <Link :href="login()">{{
                                    t('lp.nav.logIn')
                                }}</Link>
                            </Button>
                            <Button v-if="canRegister" as-child>
                                <Link :href="register()">{{
                                    t('lp.nav.getStarted')
                                }}</Link>
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
