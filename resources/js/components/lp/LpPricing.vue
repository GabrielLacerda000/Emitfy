<script setup lang="ts">
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { register } from '@/routes';
import LpPricingCard from './LpPricingCard.vue';
import LpSection from './LpSection.vue';

const { t } = useI18n();

const starterFeatures = computed(() => [
    { text: t('lp.pricing.features.clients20'), included: true },
    { text: t('lp.pricing.features.emails50'), included: true },
    { text: t('lp.pricing.features.unlimitedInvoices'), included: true },
    { text: t('lp.pricing.features.basicDashboard'), included: true },
    { text: t('lp.pricing.features.emailSupport'), included: true },
    { text: t('lp.pricing.features.recurringInvoices'), included: false },
]);

const proFeatures = computed(() => [
    { text: t('lp.pricing.features.clients300'), included: true },
    { text: t('lp.pricing.features.emails1000'), included: true },
    { text: t('lp.pricing.features.unlimitedInvoices'), included: true },
    { text: t('lp.pricing.features.recurringInvoices'), included: true },
    { text: t('lp.pricing.features.advancedReports'), included: true },
    { text: t('lp.pricing.features.whatsappSupport'), included: true },
    { text: t('lp.pricing.features.prioritySupport'), included: true },
]);

const businessFeatures = computed(() => [
    { text: t('lp.pricing.features.unlimitedClients'), included: true },
    { text: t('lp.pricing.features.emails5000'), included: true },
    { text: t('lp.pricing.features.everythingInPro'), included: true },
    { text: t('lp.pricing.features.dedicatedSupport'), included: true },
]);
</script>

<template>
    <LpSection background="muted" id="pricing" class="py-24">
        <div class="mb-20 text-center">
            <h2 class="mb-4 text-4xl font-extrabold tracking-tight sm:text-5xl">
                {{ t('lp.pricing.h2') }}
                <span class="text-primary">{{ t('lp.pricing.h2highlight') }}</span>
            </h2>
            <p class="mx-auto max-w-2xl text-lg text-muted-foreground">
                {{ t('lp.pricing.subtitle') }}
            </p>
        </div>

        <div
            class="mx-auto grid max-w-7xl grid-cols-1 gap-8 px-4 lg:grid-cols-3"
        >
            <!-- STARTER -->
            <LpPricingCard
                :name="t('lp.pricing.plans.starter.name')"
                :price="t('lp.pricing.plans.starter.price')"
                :description="t('lp.pricing.plans.starter.desc')"
                :features="starterFeatures"
                :cta="t('lp.pricing.plans.starter.cta')"
                :cta-href="register().url"
            />

            <!-- PRO (MOST POPULAR) -->
            <LpPricingCard
                :name="t('lp.pricing.plans.pro.name')"
                :price="t('lp.pricing.plans.pro.price')"
                highlighted
                :description="t('lp.pricing.plans.pro.desc')"
                :features="proFeatures"
                :cta="t('lp.pricing.plans.pro.cta')"
                :cta-href="register().url"
                :savings="t('lp.pricing.plans.pro.savings')"
            />

            <!-- BUSINESS -->
            <LpPricingCard
                :name="t('lp.pricing.plans.business.name')"
                :price="t('lp.pricing.plans.business.price')"
                :description="t('lp.pricing.plans.business.desc')"
                :features="businessFeatures"
                :cta="t('lp.pricing.plans.business.cta')"
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
                    <h4 class="text-lg font-bold">{{ t('lp.pricing.whyPro.title') }}</h4>
                    <p class="text-sm text-muted-foreground">
                        {{ t('lp.pricing.whyPro.desc') }}
                    </p>
                </div>
                <div class="text-2xl font-black text-primary">{{ t('lp.pricing.bestValue') }}</div>
            </div>
        </div>
    </LpSection>
</template>
