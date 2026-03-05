<script setup lang="ts">
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { register } from '@/routes';
import LpPricingCard from './LpPricingCard.vue';
import LpSection from './LpSection.vue';

const { t } = useI18n();

const features = computed(() => [
    { text: t('lp.pricing.features.unlimitedClients'), included: true },
    { text: t('lp.pricing.features.unlimitedInvoices'), included: true },
    { text: t('lp.pricing.features.recurringInvoices'), included: true },
    { text: t('lp.pricing.features.automaticReminders'), included: true },
    { text: t('lp.pricing.features.fullDashboard'), included: true },
    { text: t('lp.pricing.features.emailSupport'), included: true },
]);
</script>

<template>
    <LpSection background="muted" id="pricing" class="py-24">
        <div class="mb-20 text-center">
            <h2 class="mb-4 text-4xl font-extrabold tracking-tight sm:text-5xl">
                {{ t('lp.pricing.h2') }}
            </h2>
            <p class="mx-auto max-w-2xl text-lg text-muted-foreground">
                {{ t('lp.pricing.subtitle') }}
            </p>
        </div>

        <div class="mx-auto max-w-xl px-4">
            <LpPricingCard
                :name="t('lp.pricing.plan.name')"
                :price="t('lp.pricing.plan.price')"
                :description="t('lp.pricing.plan.desc')"
                :features="features"
                :cta="t('lp.pricing.plan.cta')"
                :cta-href="register().url"
            />
        </div>
    </LpSection>
</template>
